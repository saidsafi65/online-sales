<?php
namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller {

    // عرض جميع المستخدمين
    public function index(Request $request) {
        // ✅ ضع الفحص هنا مباشرة
        if (!auth()->user()->isAdmin()) {
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
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $branches = Branch::all();
        $roles = ['admin' => 'مدير النظام', 'manager' => 'مدير الفرع', 'employee' => 'موظف'];

        return view('users.create', compact('branches', 'roles'));
    }

    // حفظ المستخدم الجديد
    public function store(Request $request) {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', Password::min(8)],
            'branch_id' => 'required|exists:branches,id',
            'role' => 'required|in:admin,manager,employee',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'هذا البريد مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'branch_id.required' => 'يجب اختيار فرع',
            'role.required' => 'يجب اختيار دور',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'],
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    // عرض صفحة تعديل المستخدم
    public function edit(User $user) {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $branches = Branch::all();
        $roles = ['admin' => 'مدير النظام', 'manager' => 'مدير الفرع', 'employee' => 'موظف'];
        $statuses = ['active' => 'نشط', 'inactive' => 'غير نشط'];

        return view('users.edit', compact('user', 'branches', 'roles', 'statuses'));
    }

    // تحديث المستخدم
    public function update(Request $request, User $user) {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'branch_id' => 'required|exists:branches,id',
            'role' => 'required|in:admin,manager,employee',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'branch_id' => $validated['branch_id'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    // حذف المستخدم
    public function destroy(User $user) {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $user->delete();

        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }
}