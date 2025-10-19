@extends('layout.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="container-fluid">
    <div style="max-width: 700px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 2rem; color: #1e293b;">➕ إضافة مستخدم جديد</h1>

        <div class="card" style="border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الاسم الكامل</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="أدخل اسم المستخدم" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="example@email.com" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">كلمة المرور</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="أدخل كلمة مرور قوية" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الفرع</label>
                    <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                        <option value="">-- اختر فرع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الدور / الصلاحيات</label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                        <option value="">-- اختر دور --</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem;">
                        ✅ إضافة المستخدم
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