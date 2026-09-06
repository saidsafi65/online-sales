<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TenantDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Cross-tenant oversight for the Super Admin: view/add/delete/suspend/reset
 * any account in any store's own database.
 */
class PlatformAccountController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Tenant::on('central')->orderBy('name')->get();
        $selectedTenant = null;
        $users = collect();
        $customers = collect();
        $branches = collect();

        if ($request->filled('tenant')) {
            $selectedTenant = $tenants->firstWhere('id', (int) $request->input('tenant'));

            if ($selectedTenant) {
                $connection = TenantDatabase::connect($selectedTenant);
                $users = DB::connection($connection)->table('users')
                    ->select('id', 'name', 'email', 'role', 'status')->orderBy('name')->get();
                $customers = DB::connection($connection)->table('customers')
                    ->select('id', 'name', 'phone', 'email', 'is_active')->orderBy('name')->get();
                $branches = DB::connection($connection)->table('branches')
                    ->select('id', 'name')->orderBy('name')->get();
            }
        }

        return view('system-admin.accounts.index', compact('tenants', 'selectedTenant', 'users', 'customers', 'branches'));
    }

    public function storeUser(Request $request, Tenant $tenant)
    {
        $connection = TenantDatabase::connect($tenant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:{$connection}.users,email",
            'password' => ['required', Password::min(8)],
            'role' => 'required|in:admin,manager,employee',
            'branch_id' => "required|exists:{$connection}.branches,id",
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'هذا البريد مسجّل مسبقاً بهالمعرض',
            'password.required' => 'كلمة المرور مطلوبة',
            'role.required' => 'الدور مطلوب',
            'branch_id.required' => 'الفرع مطلوب',
            'branch_id.exists' => 'الفرع غير موجود بهالمعرض',
        ]);

        DB::connection($connection)->table('users')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'branch_id' => $validated['branch_id'],
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function destroyUser(Tenant $tenant, int $user)
    {
        $connection = TenantDatabase::connect($tenant);
        $deleted = DB::connection($connection)->table('users')->where('id', $user)->delete();
        abort_if(! $deleted, 404);

        return back()->with('success', 'تم حذف المستخدم نهائياً');
    }

    public function toggleUser(Tenant $tenant, int $user)
    {
        $connection = TenantDatabase::connect($tenant);
        $row = DB::connection($connection)->table('users')->where('id', $user)->first();
        abort_if(! $row, 404);

        DB::connection($connection)->table('users')->where('id', $user)
            ->update(['status' => $row->status === 'active' ? 'inactive' : 'active']);

        return back()->with('success', 'تم تحديث حالة الحساب');
    }

    public function resetUserPassword(Tenant $tenant, int $user)
    {
        $connection = TenantDatabase::connect($tenant);
        abort_if(! DB::connection($connection)->table('users')->where('id', $user)->exists(), 404);

        $newPassword = Str::password(12);
        DB::connection($connection)->table('users')->where('id', $user)
            ->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "كلمة المرور الجديدة: {$newPassword} (احفظها الآن، ما رح تتعاد)");
    }

    public function storeCustomer(Request $request, Tenant $tenant)
    {
        $connection = TenantDatabase::connect($tenant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => "required|string|unique:{$connection}.customers,phone",
            'email' => "nullable|email|unique:{$connection}.customers,email",
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
        ], [
            'name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مسجّل مسبقاً بهالمعرض',
            'email.unique' => 'البريد مسجّل مسبقاً بهالمعرض',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        DB::connection($connection)->table('customers')->insert([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم إضافة العميل بنجاح');
    }

    public function destroyCustomer(Tenant $tenant, int $customer)
    {
        $connection = TenantDatabase::connect($tenant);
        $deleted = DB::connection($connection)->table('customers')->where('id', $customer)->delete();
        abort_if(! $deleted, 404);

        return back()->with('success', 'تم حذف العميل وسلته وكل طلباته نهائياً');
    }

    public function toggleCustomer(Tenant $tenant, int $customer)
    {
        $connection = TenantDatabase::connect($tenant);
        $row = DB::connection($connection)->table('customers')->where('id', $customer)->first();
        abort_if(! $row, 404);

        DB::connection($connection)->table('customers')->where('id', $customer)
            ->update(['is_active' => ! $row->is_active]);

        return back()->with('success', 'تم تحديث حالة حساب العميل');
    }

    public function resetCustomerPassword(Tenant $tenant, int $customer)
    {
        $connection = TenantDatabase::connect($tenant);
        abort_if(! DB::connection($connection)->table('customers')->where('id', $customer)->exists(), 404);

        $newPassword = Str::password(12);
        DB::connection($connection)->table('customers')->where('id', $customer)
            ->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "كلمة المرور الجديدة: {$newPassword} (احفظها الآن، ما رح تتعاد)");
    }
}
