@extends('layout.platform')

@section('title', 'إنشاء معرض تلقائياً')

@section('content')
    <div style="max-width: 560px; margin: 0 auto;">
        <h1 style="font-size: 1.6rem; font-weight: 900; margin-bottom: 2rem; color: #1e293b;">⚡ إنشاء معرض تلقائياً</h1>

        <div class="card" style="border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: white;">
            <div style="background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; color: #1e40af; font-size: 0.9rem;">
                💡 بس اسم المعرض والدومين — النظام بينشئ قاعدة البيانات ويبني كل الجداول تلقائياً بدون أي خطوة يدوية.
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" style="border-radius: 12px;">
                    @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('system-admin.tenants.storeAuto') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اسم المعرض</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="مثال: معرض بيسان" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الدومين</label>
                    <input type="text" name="domain" class="form-control" value="{{ old('domain') }}" placeholder="bisan.com" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اسم قاعدة البيانات (اختياري)</label>
                    <input type="text" name="db_name" class="form-control" value="{{ old('db_name') }}" placeholder="مثال: bisan" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;">
                    <small style="color:#94a3b8;">
                        سيبها فاضية والنظام بيحاول يشتق اسم من اسم المعرض تلقائياً (لو الاسم عربي وما قدر يشتق منه، بيولّد اسم عشوائي).
                        بكل الحالات رح يترحط بمقدمة الحساب تلقائياً (مثلاً <code>ximnlmmy_bisan</code>) مثل باقي قواعد البيانات.
                    </small>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem;">
                        ⚡ إنشاء المعرض الآن
                    </button>
                    <a href="{{ route('system-admin.tenants.index') }}" class="btn btn-secondary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem; text-decoration: none; text-align: center;">
                        ❌ إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
