@extends('layout.app')

@section('title', 'هوية المعرض')

@section('content')
<div class="container-fluid">
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 2rem; color: #1e293b;">🎨 هوية المعرض</h1>

        @if(session('success'))
            <div class="alert alert-success" style="border-radius: 12px;">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" style="border-radius: 12px;">❌ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 12px;">
                @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
            </div>
        @endif

        <div class="card" style="border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div style="text-align:center; margin-bottom:1.5rem;">
                <div style="width:120px; height:120px; margin:0 auto; border:2px dashed #e2e8f0; border-radius:16px; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8fafc;">
                    @if($tenant->logo_path)
                        <img src="{{ asset('storage/'.$tenant->logo_path) }}" style="width:100%; height:100%; object-fit:contain; padding:8px;">
                    @else
                        <span style="color:#94a3b8; font-size:0.8rem;">لا يوجد شعار</span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('branding.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">شعار المعرض</label>
                    <input type="file" name="logo" accept="image/*" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.75rem;">
                    <small style="color:#94a3b8;">أي مقاس بيظهر صح تلقائياً — الحد الأقصى 1 ميجا.</small>
                </div>

                <hr style="margin: 1.75rem 0; border-color: #e2e8f0;">
                <label class="form-label" style="font-weight: 700; margin-bottom: 1rem; display:block;">🖋️ الختم والتوقيع (للفواتير والمطالبات المالية)</label>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">ختم المعرض</label>
                        <div style="width:100%; height:90px; border:2px dashed #e2e8f0; border-radius:12px; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8fafc; margin-bottom:0.5rem;">
                            @if($tenant->stamp_path)
                                <img src="{{ asset('storage/'.$tenant->stamp_path) }}" style="max-width:100%; max-height:100%; object-fit:contain; padding:6px;">
                            @else
                                <span style="color:#94a3b8; font-size:0.75rem;">لا يوجد ختم</span>
                            @endif
                        </div>
                        <input type="file" name="stamp" accept="image/png" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.6rem; font-size:0.85rem;">
                    </div>
                    <div class="col-6">
                        <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">توقيع (خط اليد)</label>
                        <div style="width:100%; height:90px; border:2px dashed #e2e8f0; border-radius:12px; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8fafc; margin-bottom:0.5rem;">
                            @if($tenant->signature_path)
                                <img src="{{ asset('storage/'.$tenant->signature_path) }}" style="max-width:100%; max-height:100%; object-fit:contain; padding:6px;">
                            @else
                                <span style="color:#94a3b8; font-size:0.75rem;">لا يوجد توقيع</span>
                            @endif
                        </div>
                        <input type="file" name="signature" accept="image/png" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.6rem; font-size:0.85rem;">
                    </div>
                </div>
                <small style="color:#94a3b8; display:block; margin-bottom:1rem;">لازم يكونوا صور PNG بخلفية مفرغة (شفافة) — رح يظهروا تلقائياً بالفواتير والمطالبات المالية بدل الختم الافتراضي.</small>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">قوالب ألوان جاهزة</label>
                    <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
                        @php
                            $templates = [
                                ['name' => 'أحمر كلاسيكي', 'primary' => '#dc2626', 'accent' => '#991b1b'],
                                ['name' => 'أزرق', 'primary' => '#2563eb', 'accent' => '#1e3a8a'],
                                ['name' => 'أخضر', 'primary' => '#16a34a', 'accent' => '#14532d'],
                                ['name' => 'بنفسجي', 'primary' => '#7c3aed', 'accent' => '#4c1d95'],
                                ['name' => 'برتقالي', 'primary' => '#ea580c', 'accent' => '#9a3412'],
                                ['name' => 'رمادي داكن', 'primary' => '#475569', 'accent' => '#1e293b'],
                            ];
                        @endphp
                        @foreach ($templates as $tpl)
                            <button type="button" onclick="applyTemplate('{{ $tpl['primary'] }}', '{{ $tpl['accent'] }}')"
                                    title="{{ $tpl['name'] }}"
                                    style="width:38px; height:38px; border-radius:50%; border:2px solid #e2e8f0; cursor:pointer; background:linear-gradient(135deg, {{ $tpl['primary'] }} 50%, {{ $tpl['accent'] }} 50%);">
                            </button>
                        @endforeach
                    </div>
                    <small style="color:#94a3b8;">اضغط أي قالب حتى يعبّي الألوان تلقائياً بالأسفل، وتقدر تعدّلها بعدين.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اللون الأساسي</label>
                    <input type="color" id="primaryColorInput" name="brand_primary_color" value="{{ $tenant->brand_primary_color ?? '#dc2626' }}" class="form-control form-control-color" style="border-radius: 10px; width:100%; height:50px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اللون الثانوي</label>
                    <input type="color" id="accentColorInput" name="brand_accent_color" value="{{ $tenant->brand_accent_color ?? '#991b1b' }}" class="form-control form-control-color" style="border-radius: 10px; width:100%; height:50px;">
                </div>

                <hr style="margin: 1.75rem 0; border-color: #e2e8f0;">
                <label class="form-label" style="font-weight: 700; margin-bottom: 1rem; display:block;">📞 معلومات التواصل</label>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">رقم الهاتف</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $tenant->contact_phone) }}" placeholder="مثال: 0599123456" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.75rem;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">رقم الواتساب</label>
                    <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $tenant->contact_whatsapp) }}" placeholder="مثال: 970599123456+" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.75rem;">
                    <small style="color:#94a3b8;">حط الرقم مع رمز الدولة (بدون + أو أصفار بالبداية) حتى يشتغل رابط واتساب صح.</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.875rem; border-radius: 10px; font-weight: 600; margin-top:1rem;">
                    💾 حفظ التعديلات
                </button>
            </form>
        </div>

        <div class="card" style="border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-top:1.5rem;">
            <h2 style="font-size:1.3rem; font-weight:800; margin-bottom:0.4rem; color:#1e293b;">📲 أيقونة التطبيق</h2>
            <p style="color:#64748b; font-size:0.88rem; margin-bottom:1.25rem;">هاي الأيقونة يلي بتظهر لما حدا يثبّت متجرك كتطبيق على جواله. حدد مكان وحجم القص بنفسك.</p>

            @if($iconExists)
                <div style="text-align:center; margin-bottom:1.25rem;">
                    <img src="{{ $iconUrl }}?v={{ time() }}" style="width:80px; height:80px; border-radius:18px; box-shadow:0 4px 10px rgba(0,0,0,.15);">
                    <div style="font-size:0.78rem; color:#94a3b8; margin-top:0.4rem;">الأيقونة الحالية</div>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اختر صورة للقص (أو استخدم الشعار الحالي تلقائياً)</label>
                <input type="file" id="iconSourceInput" accept="image/png,image/jpeg" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.75rem;">
            </div>

            <div id="cropperEmptyHint" style="color:#94a3b8; font-size:0.85rem; text-align:center; padding:1.5rem; {{ $tenant->logo_path ? 'display:none;' : '' }}">
                ارفع شعار أولاً من فوق أو اختر صورة هون حتى تقدر تقصها.
            </div>

            <div id="cropperWrap" style="max-width:380px; margin:0 auto 1rem; {{ $tenant->logo_path ? '' : 'display:none;' }}">
                <div style="position:relative; max-height:380px; overflow:hidden;">
                    <img id="cropperImage" src="" style="display:block; max-width:100%;">
                </div>
                <div style="display:flex; justify-content:center; gap:0.5rem; margin-top:0.75rem;">
                    <button type="button" id="zoomInBtn" class="btn btn-sm btn-outline-secondary">🔍 تكبير</button>
                    <button type="button" id="zoomOutBtn" class="btn btn-sm btn-outline-secondary">🔍 تصغير</button>
                </div>
                <small style="color:#94a3b8; display:block; text-align:center; margin-top:0.5rem;">اسحب الصورة لتحديد مكان القص، وكبّر/صغّر حسب الحاجة. الدائرة توضح المنطقة الآمنة يلي رح تضل ظاهرة بكل الأجهزة.</small>
            </div>

            <button type="button" id="saveIconBtn" class="btn btn-primary" style="width:100%; padding:0.875rem; border-radius:10px; font-weight:600; {{ $tenant->logo_path ? '' : 'display:none;' }}">
                💾 حفظ الأيقونة
            </button>
            <div id="iconSaveStatus" style="margin-top:0.75rem; text-align:center; font-size:0.85rem;"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
function applyTemplate(primary, accent) {
    document.getElementById('primaryColorInput').value = primary;
    document.getElementById('accentColorInput').value = accent;
}

(function () {
    const fileInput = document.getElementById('iconSourceInput');
    const wrap = document.getElementById('cropperWrap');
    const emptyHint = document.getElementById('cropperEmptyHint');
    const img = document.getElementById('cropperImage');
    const saveBtn = document.getElementById('saveIconBtn');
    const statusEl = document.getElementById('iconSaveStatus');
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    let cropper = null;

    function initCropper(src) {
        img.src = src;
        wrap.style.display = 'block';
        emptyHint.style.display = 'none';
        saveBtn.style.display = 'block';
        if (cropper) {
            cropper.destroy();
        }
        cropper = new Cropper(img, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            background: false,
            guides: false,
            center: false,
        });
    }

    @if($tenant->logo_path)
        initCropper("{{ asset('storage/'.$tenant->logo_path) }}");
    @endif

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) { initCropper(e.target.result); };
        reader.readAsDataURL(file);
    });

    if (zoomInBtn) zoomInBtn.addEventListener('click', function () { if (cropper) cropper.zoom(0.1); });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', function () { if (cropper) cropper.zoom(-0.1); });

    saveBtn.addEventListener('click', function () {
        if (!cropper) return;
        saveBtn.disabled = true;
        statusEl.textContent = 'جاري الحفظ...';

        cropper.getCroppedCanvas({ width: 512, height: 512 }).toBlob(function (blob) {
            const formData = new FormData();
            formData.append('icon_source', blob, 'icon.png');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch("{{ route('branding.icon') }}", {
                method: 'POST',
                body: formData,
            }).then(function (res) {
                window.location.href = res.url || window.location.href;
            }).catch(function () {
                statusEl.textContent = '❌ صار خطأ، حاول كمان مرة';
                saveBtn.disabled = false;
            });
        }, 'image/png');
    });
})();
</script>
@endsection
