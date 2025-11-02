@extends('layout.app')

@section('title', 'تعديل المستخدم')

@section('content')
<div class="container-fluid">
    <div style="max-width: 700px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 2rem; color: #1e293b;">✏️ تعديل المستخدم</h1>

        <div class="card" style="border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الاسم الكامل</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">كلمة المرور (اتركها فارغة لعدم التغيير)</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="أدخل كلمة مرور جديدة" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;">
                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="أعد إدخال كلمة المرور" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الفرع</label>
                    <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الدور / الصلاحيات</label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">حالة الحساب</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $user->status) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <hr style="margin: 2rem 0;">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #334155; margin-bottom: 1rem;">صلاحيات الصفحة الرئيسية</h3>
                <div class="row">
                    @php($perms = [
                        'sales' => 'المبيعات','repairs' => 'الصيانة','purchases' => 'المشتريات','catalog' => 'كتالوج المنتجات',
                        'deposits' => 'أمانات الصيانة','reports' => 'التقارير','obligations' => 'التزامات المحل الشهرية','invoices' => 'الفواتير',
                        'compatibility' => 'التوافقات','customer_orders' => 'طلبات الزبائن','daily_handovers' => 'التسليمات اليومية','returned_goods' => 'البضائع المرجعة',
                        'store' => 'المخزن الخارجي','debts' => 'الديون','backup' => 'النسخ الاحتياطي','maintenance_parts' => 'قطع الصيانة','products' => 'إدارة المنتجات',
                    ])
                    @foreach($perms as $key => $label)
                        @php($field = 'can_view_' . $key)
                        <div class="col-12 col-sm-6 col-lg-4 mb-2">
                            <label style="display:flex; gap:.5rem; align-items:center;">
                                <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $user->{$field}) ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem;">
                        ✅ حفظ التعديلات
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem; text-decoration: none; text-align: center;">
                        ❌ إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection