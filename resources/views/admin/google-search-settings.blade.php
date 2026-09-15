@extends('layout.app')

@section('title', 'خدمات Google')

@section('content')
<div class="container-fluid">
    <div style="max-width: 700px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem; color: #1e293b;">🔍🤖 خدمات Google (بحث + مساعد ذكي)</h1>
        <p style="color:#64748b; margin-bottom:1.5rem;">
            لما يبحث موظف عن موديل جهاز بصفحة "التوافقات" (متطابقات الأجهزة)، هالبيانات بتخلي البرنامج يبحث فعلياً
            بالإنترنت ويطلع نتائج حقيقية (عناوين ومقتطفات وروابط) يقدر الموظف يقرأها ويتأكد منها، وبعدين يدخل
            المواصفات المؤكدة يدوياً بنفس نموذج إضافة القطعة الموجود. كمان تقدر تفعّل "المساعد الذكي" (ودجت محادثة
            تظهر بكل صفحات الموقع، زي الماسنجر) يرد على أي سؤال يكتبه الموظف مباشرة.
        </p>

        @if(session('success'))
            <div class="alert alert-success" style="border-radius: 12px;">✅ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 12px;">
                @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('google-search-settings.update') }}">
            @csrf

            @php
                $hasKey = ! empty($tenant->google_search_api_key);
            @endphp

            <div class="card" style="border-radius: 18px; padding: 1.75rem; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem;">
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; font-size:0.9rem;">Google API Key</label>
                    <input type="password" name="google_search_api_key" class="form-control"
                           placeholder="{{ $hasKey ? '•••••••• (محفوظ — اتركه فاضي لو ما بدك تغيّره)' : 'مفتاح Google Custom Search API' }}"
                           style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.65rem 1rem;" dir="ltr" autocomplete="new-password">
                    <small style="color:#94a3b8;">لأمانك، ما بنعرض المفتاح المحفوظ هون — اكتب قيمة جديدة بس لو بدك تغيّره.</small>
                </div>

                <div class="mb-0">
                    <label class="form-label" style="font-weight: 600; font-size:0.9rem;">Search Engine ID (cx)</label>
                    <input type="text" name="google_search_cx" class="form-control"
                           value="{{ old('google_search_cx', $tenant->google_search_cx) }}"
                           placeholder="مثال: a1b2c3d4e5f6g7h8i"
                           style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.65rem 1rem;" dir="ltr">
                </div>
            </div>

            <div class="alert alert-info" style="border-radius: 12px; font-size: 0.9rem;">
                💡 <strong>كيف تجيب مفتاح البحث مجاناً؟</strong>
                <ol style="margin: 0.5rem 0 0; padding-inline-start: 1.25rem;">
                    <li>افتح <a href="https://programmablesearchengine.google.com/" target="_blank" rel="noopener">programmablesearchengine.google.com</a> وسوّي محرك بحث جديد (اختر "ابحث بكل الإنترنت")، وانسخ الـ Search Engine ID (cx).</li>
                    <li>افتح <a href="https://console.cloud.google.com/apis/library/customsearch.googleapis.com" target="_blank" rel="noopener">Google Cloud Console</a>، فعّل "Custom Search API"، وسوّي API Key من صفحة Credentials.</li>
                    <li>الحصة المجانية 100 عملية بحث باليوم — كافية لمحل صغير. إذا احتجت أكتر، فيه تسعير إضافي من Google نفسها.</li>
                </ol>
            </div>

            @php
                $hasGeminiKey = ! empty($tenant->gemini_api_key);
            @endphp

            <div class="card" style="border-radius: 18px; padding: 1.75rem; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin: 1.5rem 0;">
                <h5 style="margin:0 0 1rem; font-weight:800; color:#1e293b;">🤖 المساعد الذكي (Gemini)</h5>
                <div class="mb-0">
                    <label class="form-label" style="font-weight: 600; font-size:0.9rem;">Gemini API Key</label>
                    <input type="password" name="gemini_api_key" class="form-control"
                           placeholder="{{ $hasGeminiKey ? '•••••••• (محفوظ — اتركه فاضي لو ما بدك تغيّره)' : 'مفتاح Gemini API' }}"
                           style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.65rem 1rem;" dir="ltr" autocomplete="new-password">
                    <small style="color:#94a3b8;">لأمانك، ما بنعرض المفتاح المحفوظ هون — اكتب قيمة جديدة بس لو بدك تغيّره.</small>
                </div>
            </div>

            <div class="alert alert-info" style="border-radius: 12px; font-size: 0.9rem;">
                💡 <strong>كيف تجيب مفتاح Gemini مجاناً؟</strong>
                <ol style="margin: 0.5rem 0 0; padding-inline-start: 1.25rem;">
                    <li>افتح <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">aistudio.google.com/apikey</a> وسجّل دخول بحساب Google.</li>
                    <li>اضغط "Create API Key" وانسخ المفتاح.</li>
                    <li>الحصة المجانية كافية للاستخدام اليومي العادي لمحل صغير، وما بتحتاج بطاقة ائتمان للبدء.</li>
                </ol>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.875rem; border-radius: 10px; font-weight: 600;">
                💾 حفظ الإعدادات
            </button>
        </form>
    </div>
</div>
@endsection
