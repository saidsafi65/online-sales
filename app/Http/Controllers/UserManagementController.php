<?php
namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ChatMember;
use App\Models\User;
use App\Support\PermissionRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller {

    // عرض جميع المستخدمين
    public function index(Request $request) {
        if (!auth()->user()->canViewSection('users')) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $query = User::query();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // تصفية حسب الفرع
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // تصفية حسب الدور
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->with('branch')->paginate(15)->withQueryString();
        $branches = Branch::all();

        return view('users.index', compact('users', 'branches'));
    }

    // عرض صفحة إضافة مستخدم جديد
    public function create() {
        if (!auth()->user()->hasPermission('users.create')) {
            abort(403);
        }

        $branches = Branch::all();
        $roles = ['admin' => 'مدير النظام', 'manager' => 'مدير الفرع', 'employee' => 'موظف'];
        $registry = PermissionRegistry::all();
        $isActorAdmin = auth()->user()->isAdmin();

        return view('users.create', compact('branches', 'roles', 'registry', 'isActorAdmin'));
    }

    // حفظ المستخدم الجديد
    public function store(Request $request) {
        $actor = auth()->user();
        if (!$actor->hasPermission('users.create')) {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', Password::min(8)],
            'branch_id' => 'required|exists:branches,id',
        ];
        // الدور والصلاحيات وتصنيف "معرض جوال فقط" ما بينحددوا إلا من قبل المدير
        if ($actor->isAdmin()) {
            $rules['role'] = 'required|in:admin,manager,employee';
            $rules['permissions'] = 'nullable|array';
            $rules['permissions.*'] = 'string';
        }

        $validated = $request->validate($rules, [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'هذا البريد مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'branch_id.required' => 'يجب اختيار فرع',
            'role.required' => 'يجب اختيار دور',
        ]);

        if ($actor->isAdmin()) {
            $isMobileOnly = $request->boolean('is_mobile_shop_only');
            $role = $validated['role'];
            $permissions = $isMobileOnly
                ? PermissionRegistry::fullGrantFor('mobile_shop')
                : collect($validated['permissions'] ?? [])
                    ->filter(fn ($key) => PermissionRegistry::isValidKey($key))
                    ->values()->all();
        } else {
            // موظف غير مدير يملك صلاحية "users.create" فقط: ينشئ موظف عادي بلا صلاحيات،
            // والمدير هو اللي بيرجع يحدد صلاحياته لاحقاً من شاشة التعديل
            $isMobileOnly = false;
            $role = 'employee';
            $permissions = [];
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'],
            'role' => $role,
            'status' => 'active',
            'permissions' => $permissions,
            'is_mobile_shop_only' => $isMobileOnly,
        ]);

        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    // عرض صفحة تعديل المستخدم
    public function edit(User $user) {
        $actor = auth()->user();
        if (!$actor->hasPermission('users.edit')) {
            abort(403);
        }
        if (!$actor->isAdmin() && $user->isAdmin()) {
            abort(403, 'لا يمكنك تعديل حساب مسؤول النظام');
        }

        $branches = Branch::all();
        $roles = ['admin' => 'مدير النظام', 'manager' => 'مدير الفرع', 'employee' => 'موظف'];
        $statuses = ['active' => 'نشط', 'inactive' => 'غير نشط'];
        $registry = PermissionRegistry::all();
        $isActorAdmin = $actor->isAdmin();

        return view('users.edit', compact('user', 'branches', 'roles', 'statuses', 'registry', 'isActorAdmin'));
    }

    // تحديث المستخدم
    public function update(Request $request, User $user) {
        $actor = auth()->user();
        if (!$actor->hasPermission('users.edit')) {
            abort(403);
        }
        if (!$actor->isAdmin() && $user->isAdmin()) {
            abort(403, 'لا يمكنك تعديل حساب مسؤول النظام');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:8|confirmed',
        ];
        if ($actor->isAdmin()) {
            $rules['role'] = 'required|in:admin,manager,employee';
            $rules['permissions'] = 'nullable|array';
            $rules['permissions.*'] = 'string';
        }

        $validated = $request->validate($rules);

        $update = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'branch_id' => $validated['branch_id'],
            'status' => $validated['status'],
        ];

        // الدور والصلاحيات وتصنيف "معرض جوال فقط" ما بيتغيروا إلا من قبل المدير،
        // بغض النظر عمّا تم إرساله بالطلب — تبقى كما هي لغير المدير
        if ($actor->isAdmin()) {
            $isMobileOnly = $request->boolean('is_mobile_shop_only');
            $update['role'] = $validated['role'];
            $update['is_mobile_shop_only'] = $isMobileOnly;
            $update['permissions'] = $isMobileOnly
                ? PermissionRegistry::fullGrantFor('mobile_shop')
                : collect($validated['permissions'] ?? [])
                    ->filter(fn ($key) => PermissionRegistry::isValidKey($key))
                    ->values()->all();
        }

        $user->update($update);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    // حذف المستخدم — حصراً للمدير، مش صلاحية قابلة للتفويض
    public function destroy(User $user) {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        if (app()->bound('currentTenant')) {
            ChatMember::on('central')
                ->where('tenant_id', app('currentTenant')->id)
                ->where('local_user_id', $user->id)
                ->delete();
        }

        $user->delete();

        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }
}
