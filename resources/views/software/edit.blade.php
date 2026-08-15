@extends('layout.app')

@section('title', 'تعديل البرنامج')

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
    .image-drop { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 2rem; text-align: center; color: #64748b; cursor: pointer; }
    .image-drop:hover { border-color: #0891b2; color: #0891b2; }
    #imagePreview img { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; margin-top: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .current-image { width: 100px; height: 100px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.1); margin-bottom: 1rem; }
    .toggle-row { display: flex; align-items: center; gap: .75rem; padding: .5rem 0 1.25rem; }
    .toggle-switch { position: relative; width: 46px; height: 26px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; inset: 0; background: #e2e8f0; border-radius: 50px; cursor: pointer; transition: .2s; }
    .toggle-slider::before { content:''; position:absolute; width:20px; height:20px; left:3px; top:3px; background:white; border-radius:50%; transition:.2s; box-shadow:0 1px 4px rgba(0,0,0,.2); }
    .toggle-switch input:checked + .toggle-slider { background: #dc2626; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }
    .btn-submit { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: .9rem 2.2rem; border: none; border-radius: 50px; font-weight: 700; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: .6rem; box-shadow: 0 4px 15px rgba(16,185,129,.3); }
    .btn-cancel { padding: .9rem 2.2rem; border-radius: 50px; font-weight: 700; text-decoration: none; color: #64748b; border: 1.5px solid #e2e8f0; }
    .invalid-feedback { display: block; color: #dc2626; font-size: .82rem; margin-top: .3rem; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="form-header"><h1><i class="fas fa-compact-disc"></i> تعديل: {{ $software->name }}</h1></div>

    <div class="form-card">
        <form action="{{ route('software.update', $software->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label>اسم البرنامج</label>
                    <input type="text" name="name" value="{{ old('name', $software->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الشركة المطورة</label>
                    <input type="text" name="developer" value="{{ old('developer', $software->developer) }}">
                </div>

                <div class="form-group">
                    <label>الإصدار</label>
                    <input type="text" name="version" value="{{ old('version', $software->version) }}">
                </div>

                <div class="form-group">
                    <label>التصنيف</label>
                    <input type="text" name="category" value="{{ old('category', $software->category) }}">
                </div>

                <div class="form-group">
                    <label>نظام التشغيل المدعوم</label>
                    <input type="text" name="platform" value="{{ old('platform', $software->platform) }}">
                </div>

                <div class="form-group">
                    <label>نوع الترخيص</label>
                    <input type="text" name="license_type" value="{{ old('license_type', $software->license_type) }}">
                </div>

                <div class="form-group">
                    <label>السعر (₪) — اتركه فارغ إذا اتصل للسعر</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $software->price) }}">
                </div>

                <div class="form-group full">
                    <label>الوصف</label>
                    <textarea name="description" rows="4">{{ old('description', $software->description) }}</textarea>
                </div>

                <div class="form-group full">
                    <div class="toggle-row">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_out_of_stock" value="1" {{ old('is_out_of_stock', $software->is_out_of_stock) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-weight:700;color:#1e293b;">غير متوفر حالياً</span>
                    </div>
                </div>

                <div class="form-group full">
                    <label>الصورة الحالية</label>
                    @if($software->image)
                        <div><img src="{{ asset('storage/'.$software->image) }}" class="current-image" alt=""></div>
                    @else
                        <p class="hint">لا توجد صورة حالياً.</p>
                    @endif
                    <label>تغيير الصورة</label>
                    <div class="image-drop" onclick="document.getElementById('imageInput').click()">
                        <i class="fas fa-image" style="font-size:1.8rem;"></i>
                        <p style="margin:.5rem 0 0;">اضغط هنا لاختيار صورة جديدة</p>
                    </div>
                    <input type="file" id="imageInput" name="image" accept="image/*" style="display:none" onchange="previewImage(event)">
                    <div id="imagePreview"></div>
                    @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> حفظ التعديلات</button>
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
