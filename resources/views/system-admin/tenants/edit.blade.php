@extends('layout.platform')

@section('title', 'تعديل معرض')

@section('content')
    <div style="max-width: 700px; margin: 0 auto;">
        <h1 style="font-size: 1.6rem; font-weight: 900; margin-bottom: 2rem; color: #1e293b;">✏️ تعديل معرض: {{ $tenant->name }}</h1>

        <div class="card" style="border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: white;">
            <div style="background: #fff7ed; border: 2px solid #fed7aa; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; color: #9a3412; font-size: 0.9rem;">
                ⚠️ أي تعديل ببيانات قاعدة البيانات رح يترفحص فورًا قبل الحفظ.
            </div>

            <form method="POST" action="{{ route('system-admin.tenants.update', $tenant) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اسم المعرض</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الدومين</label>
                    <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror" value="{{ old('domain', $tenant->domain) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('domain')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <hr style="margin: 1.5rem 0;">
                <h5 style="font-weight: 700; margin-bottom: 1rem;">بيانات قاعدة البيانات</h5>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">عنوان السيرفر (Host)</label>
                    <input type="text" name="db_host" class="form-control @error('db_host') is-invalid @enderror" value="{{ old('db_host', $tenant->db_host) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('db_host')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">المنفذ (Port)</label>
                    <input type="text" name="db_port" class="form-control @error('db_port') is-invalid @enderror" value="{{ old('db_port', $tenant->db_port) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;">
                    @error('db_port')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اسم قاعدة البيانات</label>
                    <input type="text" name="db_database" class="form-control @error('db_database') is-invalid @enderror" value="{{ old('db_database', $tenant->db_database) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('db_database')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اسم المستخدم</label>
                    <input type="text" name="db_username" class="form-control @error('db_username') is-invalid @enderror" value="{{ old('db_username', $tenant->db_username) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('db_username')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">كلمة المرور</label>
                    <input type="password" name="db_password" class="form-control @error('db_password') is-invalid @enderror" value="{{ old('db_password', $tenant->db_password) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('db_password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem;">
                        ✅ فحص الاتصال وحفظ التعديلات
                    </button>
                    <a href="{{ route('system-admin.tenants.index') }}" class="btn btn-secondary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem; text-decoration: none; text-align: center;">
                        ❌ إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
