@extends('layout.platform')

@section('title', 'الحسابات - مدير النظام')

@section('content')
<h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b; margin-bottom: 1.5rem;">إدارة الحسابات</h1>

<form method="GET" action="{{ route('system-admin.accounts.index') }}" style="margin-bottom: 1.5rem; display:flex; gap:0.75rem; align-items:center;">
    <label style="font-weight:600;">اختر معرض:</label>
    <select name="tenant" onchange="this.form.submit()" style="border-radius:10px; border:2px solid #e2e8f0; padding:0.6rem 1rem; min-width:250px;">
        <option value="">-- اختر --</option>
        @foreach($tenants as $t)
            <option value="{{ $t->id }}" {{ $selectedTenant && $selectedTenant->id === $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->domain }})</option>
        @endforeach
    </select>
</form>

@if($selectedTenant)
    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06); margin-bottom: 2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h5 style="font-weight:700; margin:0;">👤 المستخدمين (الموظفين)</h5>
            <button type="button" class="btn btn-sm btn-primary" style="border-radius:8px; font-weight:600;" onclick="toggleForm('add-user-form')">➕ إضافة مستخدم</button>
        </div>

        <div id="add-user-form" style="display:none; background:#f8fafc; border-radius:12px; padding:1rem; margin-bottom:1.25rem;">
            <form method="POST" action="{{ route('system-admin.accounts.users.store', $selectedTenant) }}" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
                @csrf
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">الاسم</label>
                    <input type="text" name="name" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">البريد الإلكتروني</label>
                    <input type="email" name="email" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">كلمة المرور</label>
                    <input type="password" name="password" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">الدور</label>
                    <select name="role" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                        <option value="employee">موظف</option>
                        <option value="manager">مدير فرع</option>
                        <option value="admin">مدير النظام (المعرض)</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">الفرع</label>
                    <select name="branch_id" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-success" style="border-radius:8px; font-weight:600;">حفظ</button>
            </form>
            @if($branches->isEmpty())
                <p style="color:#ef4444; font-size:0.8rem; margin-top:0.5rem;">⚠️ هالمعرض ما فيه فروع مسجّلة بعد — لازم يكون في فرع واحد على الأقل قبل ما تضيف مستخدم.</p>
            @endif
        </div>

        <div style="overflow-x:auto;">
            <table class="table" style="min-width:600px;">
                <thead>
                    <tr><th>الاسم</th><th>البريد</th><th>الدور</th><th>الحالة</th><th>إجراءات</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->role }}</td>
                            <td>
                                @if($u->status === 'active')
                                    <span style="color:#22c55e; font-weight:700;">فعّال</span>
                                @else
                                    <span style="color:#ef4444; font-weight:700;">معطّل</span>
                                @endif
                            </td>
                            <td style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <form method="POST" action="{{ route('system-admin.accounts.users.toggle', [$selectedTenant, $u->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $u->status === 'active' ? 'btn-secondary' : 'btn-success' }}" onclick="return confirm('متأكد؟')">
                                        {{ $u->status === 'active' ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('system-admin.accounts.users.reset-password', [$selectedTenant, $u->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('رح يتم توليد كلمة مرور جديدة عشوائياً. متأكد؟')">
                                        إعادة تعيين كلمة المرور
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('system-admin.accounts.users.destroy', [$selectedTenant, $u->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('رح يُحذف هالمستخدم نهائياً ومستحيل نرجعه. متأكد؟')">
                                        🗑️ حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color:#94a3b8;">لا يوجد مستخدمين</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h5 style="font-weight:700; margin:0;">🛍️ العملاء</h5>
            <button type="button" class="btn btn-sm btn-primary" style="border-radius:8px; font-weight:600;" onclick="toggleForm('add-customer-form')">➕ إضافة عميل</button>
        </div>

        <div id="add-customer-form" style="display:none; background:#f8fafc; border-radius:12px; padding:1rem; margin-bottom:1.25rem;">
            <form method="POST" action="{{ route('system-admin.accounts.customers.store', $selectedTenant) }}" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
                @csrf
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">الاسم</label>
                    <input type="text" name="name" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">الهاتف</label>
                    <input type="text" name="phone" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">البريد (اختياري)</label>
                    <input type="email" name="email" style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">كلمة المرور</label>
                    <input type="password" name="password" required style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <div>
                    <label style="font-size:0.8rem; font-weight:600;">المدينة (اختياري)</label>
                    <input type="text" name="city" style="display:block; border-radius:8px; border:2px solid #e2e8f0; padding:0.5rem;">
                </div>
                <button type="submit" class="btn btn-sm btn-success" style="border-radius:8px; font-weight:600;">حفظ</button>
            </form>
        </div>

        <div style="overflow-x:auto;">
            <table class="table" style="min-width:600px;">
                <thead>
                    <tr><th>الاسم</th><th>الهاتف</th><th>البريد</th><th>الحالة</th><th>إجراءات</th></tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->phone }}</td>
                            <td>{{ $c->email }}</td>
                            <td>
                                @if($c->is_active)
                                    <span style="color:#22c55e; font-weight:700;">فعّال</span>
                                @else
                                    <span style="color:#ef4444; font-weight:700;">معطّل</span>
                                @endif
                            </td>
                            <td style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <form method="POST" action="{{ route('system-admin.accounts.customers.toggle', [$selectedTenant, $c->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $c->is_active ? 'btn-secondary' : 'btn-success' }}" onclick="return confirm('متأكد؟')">
                                        {{ $c->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('system-admin.accounts.customers.reset-password', [$selectedTenant, $c->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('رح يتم توليد كلمة مرور جديدة عشوائياً. متأكد؟')">
                                        إعادة تعيين كلمة المرور
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('system-admin.accounts.customers.destroy', [$selectedTenant, $c->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('⚠️ رح يُحذف هالعميل نهائياً — وكمان سلته وكل طلباته السابقة رح تنحذف معه (مرتبطين بحسابه). مستحيل نرجعهم. متأكد؟')">
                                        🗑️ حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color:#94a3b8;">لا يوجد عملاء</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@else
    <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06); color: #64748b;">
        اختر معرض من الأعلى لعرض حساباته.
    </div>
@endif

@push('scripts')
<script>
    function toggleForm(id) {
        const el = document.getElementById(id);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
</script>
@endpush
@endsection
