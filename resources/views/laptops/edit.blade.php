@extends('layout.app')

@section('title', 'تعديل اللابتوب')

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
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: #dc2626; }
    .form-group .hint { font-size: .78rem; color: #94a3b8; margin-top: .3rem; }
    .image-drop { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 2rem; text-align: center; color: #64748b; cursor: pointer; }
    .image-drop:hover { border-color: #dc2626; color: #dc2626; }
    #imagePreview { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1rem; }
    #imagePreview img { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .existing-gallery { display: flex; flex-wrap: wrap; gap: .85rem; margin-bottom: 1rem; }
    .existing-thumb { position: relative; width: 100px; height: 100px; }
    .existing-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .existing-thumb form { position: absolute; top: -8px; left: -8px; margin: 0; }
    .existing-thumb button {
        width: 26px; height: 26px; border-radius: 50%; border: none; background: #dc2626; color: white;
        cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,.25);
    }
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
    <div class="form-header">
        <h1><i class="fas fa-laptop"></i> تعديل: {{ $laptop->name }}</h1>
    </div>

    <div class="form-card">
        <form action="{{ route('laptops.update', $laptop->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label>اسم اللابتوب</label>
                    <input type="text" name="name" value="{{ old('name', $laptop->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الماركة</label>
                    <input type="text" name="brand" value="{{ old('brand', $laptop->brand) }}" required>
                    @error('brand') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الموديل</label>
                    <input type="text" name="model" value="{{ old('model', $laptop->model) }}">
                </div>

                <div class="form-group">
                    <label>المعالج</label>
                    <input type="text" name="processor" value="{{ old('processor', $laptop->processor) }}" required>
                    @error('processor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الرام (RAM)</label>
                    <input type="text" name="ram" value="{{ old('ram', $laptop->ram) }}" required>
                    @error('ram') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>الهارد (التخزين)</label>
                    <input type="text" name="storage" value="{{ old('storage', $laptop->storage) }}" required>
                    @error('storage') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>كرت الشاشة</label>
                    <input type="text" name="gpu" value="{{ old('gpu', $laptop->gpu) }}">
                </div>

                <div class="form-group">
                    <label>مدة البطارية</label>
                    <input type="text" name="battery_life" value="{{ old('battery_life', $laptop->battery_life) }}">
                </div>

                <div class="form-group">
                    <label>السعر (₪)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $laptop->price) }}" required>
                    @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>نسبة الخصم (%)</label>
                    <input type="number" step="0.01" name="discount" value="{{ old('discount', $laptop->discount) }}" min="0" max="100">
                </div>

                <div class="form-group full">
                    <label>الوصف</label>
                    <textarea name="description" rows="4">{{ old('description', $laptop->description) }}</textarea>
                </div>

                <div class="form-group full">
                    <div class="toggle-row">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_out_of_stock" value="1" {{ old('is_out_of_stock', $laptop->is_out_of_stock) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span style="font-weight:700;color:#1e293b;">نفذت الكمية (غير متوفر حالياً)</span>
                    </div>
                </div>

                <div class="form-group full">
                    <label>صور اللابتوب الحالية</label>
                    @if($laptop->images->count() > 0)
                        <div class="existing-gallery">
                            @foreach($laptop->images as $image)
                                <div class="existing-thumb">
                                    <img src="{{ asset('storage/'.$image->image) }}" alt="">
                                    <form action="{{ route('laptops.images.destroy', $image->id) }}" method="POST"
                                          onsubmit="return confirm('حذف هذه الصورة؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="حذف الصورة"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="hint">لا توجد صور حالياً لهذا اللابتوب.</p>
                    @endif

                    <label>إضافة صور جديدة</label>
                    <div class="image-drop" onclick="document.getElementById('imagesInput').click()">
                        <i class="fas fa-images" style="font-size:1.8rem;"></i>
                        <p style="margin:.5rem 0 0;">اضغط هنا لاختيار صور إضافية</p>
                    </div>
                    <input type="file" id="imagesInput" name="images[]" multiple accept="image/*" style="display:none" onchange="previewImages(event)">
                    <div id="imagePreview"></div>
                    @error('images.*') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> حفظ التعديلات</button>
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
