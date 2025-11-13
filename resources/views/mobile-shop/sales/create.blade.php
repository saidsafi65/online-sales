@extends('layout.app')

@section('title', 'إضافة مبيعة جديدة')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12" style="display:flex; align-items:center; gap:1rem;">
                <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
                <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                    <i class="fas fa-shopping-cart" style="color: #10b981; margin-right: 0.5rem;"></i>
                    إضافة مبيعة جديدة
                </h1>
            </div>
        </div>

        <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-body">
                <form action="{{ route('mobile-shop.sales.store') }}" method="POST">
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
                            <label for="product_type" class="form-label" style="font-weight: 600; color: #1e293b;">نوع المنتج</label>
                            <input type="text" class="form-control @error('product_type') is-invalid @enderror" 
                                   id="product_type" name="product_type" value="{{ old('product_type') }}" required>
                            @error('product_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label" style="font-weight: 600; color: #1e293b;">الكمية</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label" style="font-weight: 600; color: #1e293b;">طريقة الدفع</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                <option value="">اختر طريقة الدفع</option>
                                <option value="نقدي" {{ old('payment_method') === 'نقدي' ? 'selected' : '' }}>نقدي</option>
                                <option value="تطبيق" {{ old('payment_method') === 'تطبيق' ? 'selected' : '' }}>تطبيق</option>
                                <option value="مختلط" {{ old('payment_method') === 'مختلط' ? 'selected' : '' }}>مختلط</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cash_amount" class="form-label" style="font-weight: 600; color: #1e293b;">قيمة التكلفة نقدي</label>
                            <input type="number" class="form-control @error('cash_amount') is-invalid @enderror" 
                                   id="cash_amount" name="cash_amount" step="0.01" value="{{ old('cash_amount') ?? 0 }}" required>
                            @error('cash_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_amount" class="form-label" style="font-weight: 600; color: #1e293b;">قيمة التكلفة بنكي</label>
                            <input type="number" class="form-control @error('bank_amount') is-invalid @enderror" 
                                   id="bank_amount" name="bank_amount" step="0.01" value="{{ old('bank_amount') ?? 0 }}" required>
                            @error('bank_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> حفظ
                        </button>
                        <a href="{{ route('mobile-shop.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
