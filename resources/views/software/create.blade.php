@extends('layout.app')

@section('title', 'إضافة برنامج جديد')

@push('styles')
<style>
    .form-header { background: linear-gradient(135deg, #083344 0%, #0891b2 100%); color: white; padding: 2rem; border-radius: 20px; margin-bottom: 2rem; }
    .form-header h1 { font-size: 1.8rem; font-weight: 900; display: flex; align-items: center; gap: 1rem; margin: 0; }
    .form-card { background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,.08); padding: 2rem; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group.full { grid-column: 1 / -1; }
    .form-group label { display: block; font-weight: 700; color: #1e293b; margin-bottom: .5rem; font-size: .92rem; }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%; padding: .75rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-family: inherit; font-size: .95rem; transition: border-color .2s;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #0891b2; }
    .form-group .hint { font-size: .78rem; color: #94a3b8; margin-top: .3rem; }
    .image-drop { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 2rem; text-align: center; color: #64748b; cursor: pointer; }
    .image-drop:hover { border-color: #0891b2; color: #0891b2; }
    #imagePreview img { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; margin-top: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .btn-submit { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: .9rem 2.2rem; border: none; border-radius: 50px; font-weight: 700; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: .6rem; box-shadow: 0 4px 15px rgba(16,185,129,.3); }
    .btn-cancel { padding: .9rem 2.2rem; border-radius: 50px; font-weight: 700; text-decoration: none; color: #64748b; border: 1.5px solid #e2e8f0; }
    .invalid-feedback { display: block; color: #dc2626; font-size: .82rem; margin-top: .3rem; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="form-header"><h1><i class="fas fa-compact-disc"></i> إضافة برنامج جديد</h1></div>

    <div class="form-card">
        <form action="{{ route('software.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>اسم البرنامج</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: Microsoft Office 2024" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الشركة المطورة</label>
                    <input type="text" name="developer" value="{{ old('developer') }}" placeholder="مثال: Microsoft">
                </div>

                <div class="form-group">
                    <label>الإصدار</label>
                    <input type="text" name="version" value="{{ old('version') }}" placeholder="مثال: 2024">
                </div>

                <div class="form-group">
                    <label>التصنيف</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="مثال: مكتبي، تصميم، حماية...">
                </div>

                <div class="form-group">
                    <label>نظام التشغيل المدعوم</label>
                    <input type="text" name="platform" value="{{ old('platform') }}" placeholder="مثال: Windows / Mac / الكل">
                </div>

                <div class="form-group">
                    <label>نوع الترخيص</label>
                    <input type="text" name="license_type" value="{{ old('license_type') }}" placeholder="مثال: مدى الحياة، اشتراك سنوي">
                </div>

                <div class="form-group">
                    <label>السعر (₪) — اتركه فارغ إذا اتصل للسعر</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}">
                </div>

                <div class="form-group full">
                    <label>الوصف</label>
                    <textarea name="description" rows="4" placeholder="تفاصيل ومميزات البرنامج...">{{ old('description') }}</textarea>
                </div>

                <div class="form-group full">
                    <label>صورة / أيقونة البرنامج</label>
                    <div class="image-drop" onclick="document.getElementById('imageInput').click()">
                        <i class="fas fa-image" style="font-size:1.8rem;"></i>
                        <p style="margin:.5rem 0 0;">اضغط هنا لاختيار صورة</p>
                    </div>
                    <input type="file" id="imageInput" name="image" accept="image/*" style="display:none" onchange="previewImage(event)">
                    <div id="imagePreview"></div>
                    @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> حفظ البرنامج</button>
                <a href="{{ route('software.index-admin') }}" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.createElement('img');
        img.src = e.target.result;
        preview.appendChild(img);
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
