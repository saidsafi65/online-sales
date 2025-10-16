@extends('layout.gust')

@section('title', 'تعديل المنتج')

@push('styles')
<style>
    .form-container {
        max-width: 900px;
        margin: 3rem auto;
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        text-align: center;
    }

    .form-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
    }

    .form-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .form-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        color: #8b5cf6;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        color: #8b5cf6;
    }

    .form-control {
        padding: 0.875rem 1rem;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.15);
        outline: none;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .current-image-section {
        background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 2px solid #e9d5ff;
    }

    .current-image-title {
        font-weight: 600;
        color: #5b21b6;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .current-image-title i {
        color: #8b5cf6;
    }

    .current-image-wrapper {
        text-align: center;
    }

    .current-image {
        max-width: 250px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
    }

    .file-input-wrapper {
        position: relative;
        overflow: hidden;
    }

    .file-input-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-input-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 2rem;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-input-label:hover {
        border-color: #8b5cf6;
        background: #f3e8ff;
    }

    .file-input-label i {
        font-size: 2rem;
        color: #8b5cf6;
    }

    .file-input-text {
        text-align: center;
    }

    .file-input-text .main {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .file-input-text .sub {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .image-preview {
        margin-top: 1rem;
        text-align: center;
        display: none;
    }

    .image-preview img {
        max-width: 200px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
    }

    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2.5rem;
    }

    .btn-submit {
        flex: 1;
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        color: white;
        padding: 1rem 3rem;
        border-radius: 50px;
        border: none;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    }

    .btn-cancel {
        flex: 1;
        background: #64748b;
        color: white;
        padding: 1rem 3rem;
        border-radius: 50px;
        border: none;
        font-weight: 700;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(100, 116, 139, 0.4);
        color: white;
        background: #475569;
    }

    .alert {
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: none;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }

        .button-group {
            flex-direction: column;
        }

        .form-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <h1 class="form-title">
                <i class="fas fa-edit"></i>
                تعديل المنتج
            </h1>
            <p class="form-subtitle">تحديث بيانات المنتج</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-circle me-2"></i>يرجى تصحيح الأخطاء التالية:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="form-card">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                @method('PUT')

                <!-- معلومات المنتج -->
                <div class="mb-4">
                    <h3 class="form-section-title">
                        <span class="section-icon">
                            <i class="fas fa-box"></i>
                        </span>
                        معلومات المنتج
                    </h3>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag"></i>
                                اسم المنتج
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $product->name) }}"
                                   placeholder="مثال: لابتوب Dell"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="price" class="form-label">
                                <i class="fas fa-money-bill-wave"></i>
                                السعر (بالشيكل)
                            </label>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   step="0.01" 
                                   value="{{ old('price', $product->price) }}"
                                   placeholder="0.00"
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left"></i>
                                الوصف
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      placeholder="أدخل وصف المنتج...">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- صورة المنتج الحالية -->
                @if($product->image)
                    <div class="current-image-section">
                        <div class="current-image-title">
                            <i class="fas fa-image"></i>
                            الصورة الحالية
                        </div>
                        <div class="current-image-wrapper">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="current-image">
                        </div>
                    </div>
                @endif

                <!-- صورة المنتج الجديدة -->
                <div class="mb-4">
                    <h3 class="form-section-title">
                        <span class="section-icon">
                            <i class="fas fa-image"></i>
                        </span>
                        تغيير صورة المنتج
                    </h3>

                    <div class="file-input-wrapper">
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               onchange="previewImage(this)">
                        <label for="image" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div class="file-input-text">
                                <div class="main">انقر أو اسحب صورة هنا</div>
                                <div class="sub">صيغ مقبولة: JPG, PNG, GIF (اختياري)</div>
                            </div>
                        </label>
                    </div>

                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="" alt="معاينة الصورة">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        <span>حفظ التعديلات</span>
                    </button>
                    <a href="{{ route('products.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        <span>إلغاء</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection