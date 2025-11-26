@extends('layout.app')

@section('title', 'إضافة منتج جديد')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12" style="display:flex; align-items:center; gap:1rem;">
                <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
                <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                    <i class="fas fa-boxes" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                    إضافة منتج جديد
                </h1>
            </div>
        </div>

        <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-body">
                <form action="{{ route('mobile-shop.inventory.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label" style="font-weight: 600; color: #1e293b;">اسم المنتج</label>
                            <input type="text" class="form-control @error('product_name') is-invalid @enderror" 
                                   id="product_name" name="product_name" value="{{ old('product_name') }}" required>
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="model_type" class="form-label" style="font-weight: 600; color: #1e293b;">النوع / الموديل</label>
                            <input type="text" class="form-control @error('model_type') is-invalid @enderror" 
                                   id="model_type" name="model_type" value="{{ old('model_type') }}" required>
                            @error('model_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label" style="font-weight: 600; color: #1e293b;">الكمية</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                            <small class="text-muted" id="available_quantity"></small>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="wholesale_price" class="form-label" style="font-weight: 600; color: #1e293b;">سعر الجملة</label>
                            <input type="number" class="form-control @error('wholesale_price') is-invalid @enderror" 
                                   id="wholesale_price" name="wholesale_price" step="0.01" value="{{ old('wholesale_price') }}" required>
                            @error('wholesale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="selling_price" class="form-label" style="font-weight: 600; color: #1e293b;">سعر البيع</label>
                        <input type="number" class="form-control @error('selling_price') is-invalid @enderror" 
                               id="selling_price" name="selling_price" step="0.01" value="{{ old('selling_price') }}" required>
                        @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ
                        </button>
                        <a href="{{ route('mobile-shop.inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
