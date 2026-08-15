@extends('layout.app')

@section('title', 'إضافة لابتوب جديد')

@push('styles')
<style>
    .form-header { background: linear-gradient(135deg, #0f172a 0%, #334155 100%); color: white; padding: 2rem; border-radius: 20px; margin-bottom: 2rem; }
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
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #4f46e5; }
    .form-group .hint { font-size: .78rem; color: #94a3b8; margin-top: .3rem; }
    .image-drop {
        border: 2px dashed #cbd5e1; border-radius: 12px; padding: 2rem; text-align: center; color: #64748b; cursor: pointer;
    }
    .image-drop:hover { border-color: #4f46e5; color: #4f46e5; }
    #imagePreview { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1rem; }
    #imagePreview img { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .btn-submit {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: .9rem 2.2rem; border: none;
        border-radius: 50px; font-weight: 700; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: .6rem;
        box-shadow: 0 4px 15px rgba(16,185,129,.3);
    }
    .btn-cancel { padding: .9rem 2.2rem; border-radius: 50px; font-weight: 700; text-decoration: none; color: #64748b; border: 1.5px solid #e2e8f0; }
    .invalid-feedback { display: block; color: #dc2626; font-size: .82rem; margin-top: .3rem; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container">
    <div class="form-header">
        <h1><i class="fas fa-laptop"></i> إضافة لابتوب جديد</h1>
    </div>

    <div class="form-card">
        <form action="{{ route('laptops.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>اسم اللابتوب</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="مثال: Dell XPS 15" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الماركة</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" placeholder="مثال: Dell, HP, Lenovo" required>
                    @error('brand') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الموديل</label>
                    <input type="text" name="model" value="{{ old('model') }}" placeholder="مثال: XPS 9530">
                    @error('model') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>المعالج</label>
                    <input type="text" name="processor" value="{{ old('processor') }}" placeholder="مثال: Intel Core i7-13700H" required>
                    @error('processor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الرام (RAM)</label>
                    <input type="text" name="ram" value="{{ old('ram') }}" placeholder="مثال: 16GB DDR5" required>
                    @error('ram') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الهارد (التخزين)</label>
                    <input type="text" name="storage" value="{{ old('storage') }}" placeholder="مثال: 512GB SSD" required>
                    @error('storage') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>كرت الشاشة</label>
                    <input type="text" name="gpu" value="{{ old('gpu') }}" placeholder="مثال: NVIDIA RTX 4060 6GB">
                    @error('gpu') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>مدة البطارية</label>
                    <input type="text" name="battery_life" value="{{ old('battery_life') }}" placeholder="مثال: حتى 10 ساعات">
                    @error('battery_life') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>السعر (₪)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required>
                    @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>نسبة الخصم (%)</label>
                    <input type="number" step="0.01" name="discount" value="{{ old('discount', 0) }}" min="0" max="100">
                    @error('discount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full">
                    <label>الوصف</label>
                    <textarea name="description" rows="4" placeholder="تفاصيل إضافية عن اللابتوب...">{{ old('description') }}</textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full">
                    <label>صور اللابتوب (يمكن اختيار أكثر من صورة)</label>
                    <div class="image-drop" onclick="document.getElementById('imagesInput').click()">
                        <i class="fas fa-images" style="font-size:1.8rem;"></i>
                        <p style="margin:.5rem 0 0;">اضغط هنا لاختيار الصور</p>
                    </div>
                    <input type="file" id="imagesInput" name="images[]" multiple accept="image/*" style="display:none" onchange="previewImages(event)">
                    <div class="hint">الصورة الأولى تُستخدم كصورة غلاف في صفحة العرض العامة.</div>
                    <div id="imagePreview"></div>
                    @error('images') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    @error('images.*') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> حفظ اللابتوب</button>
                <a href="{{ route('laptops.index-admin') }}" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImages(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    Array.from(event.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
