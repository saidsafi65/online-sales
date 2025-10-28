@extends('layout.app')

@section('title', 'تعديل المنتج')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">✏️ تعديل المنتج</h1>
    <p class="welcome-subtitle">تحديث بيانات المنتج في الكتالوج</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <!-- عرض رسائل الخطأ -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 15px; border: none; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; margin-bottom: 2rem;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>يرجى تصحيح الأخطاء التالية:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- نموذج تعديل المنتج -->
        <form action="{{ route('catalog.update', $item->id) }}" method="POST" id="catalogForm">
            @csrf
            @method('PUT')

            <!-- معلومات المنتج الأساسية -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-box" style="color: #1e40af;"></i>
                    معلومات المنتج
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="product" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-tag" style="color: #1e40af;"></i>
                            اسم المنتج
                        </label>
                        <input type="text" 
                               class="form-control @error('product') is-invalid @enderror" 
                               id="product" 
                               name="product" 
                               value="{{ old('product', $item->product) }}"
                               placeholder="مثال: لابتوب Dell Latitude"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                               required>
                        @error('product')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-microchip" style="color: #6366f1;"></i>
                            النوع / الموديل
                        </label>
                        <input type="text" 
                               class="form-control @error('type') is-invalid @enderror" 
                               id="type" 
                               name="type" 
                               value="{{ old('type', $item->type) }}"
                               placeholder="مثال: i7 - 16GB RAM"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                               required>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="quantity" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-warehouse" style="color: #10b981;"></i>
                            الكمية المتوفرة
                        </label>
                        <input type="number" 
                               class="form-control @error('quantity') is-invalid @enderror" 
                               id="quantity" 
                               name="quantity" 
                               value="{{ old('quantity', $item->quantity) }}"
                               min="0"
                               step="1"
                               placeholder="0"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                               required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" style="display: block; margin-top: 0.5rem;">
                            <i class="fas fa-info-circle"></i> الكمية الحالية في المخزون
                        </small>
                    </div>
                </div>
            </div>

            <!-- الأسعار -->
            <div class="service-card card-success mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-coins" style="color: #10b981;"></i>
                    الأسعار
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="wholesale_price" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-hand-holding-usd" style="color: #059669;"></i>
                            سعر الجملة
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('wholesale_price') is-invalid @enderror" 
                                   id="wholesale_price" 
                                   name="wholesale_price" 
                                   value="{{ old('wholesale_price', $item->wholesale_price) }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="0.00"
                                   style="padding: 0.75rem 1rem; border-radius: 10px 0 0 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                                   oninput="calculateProfit()"
                                   required>
                            <span class="input-group-text" style="background: #f8fafc; border: 2px solid #e2e8f0; border-right: none; border-radius: 0 10px 10px 0; font-weight: 600; color: #64748b;">شيكل</span>
                            @error('wholesale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted" style="display: block; margin-top: 0.5rem;">
                            <i class="fas fa-shopping-cart"></i> سعر الشراء من المورد
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="sale_price" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-money-bill-wave" style="color: #10b981;"></i>
                            سعر البيع
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control @error('sale_price') is-invalid @enderror" 
                                   id="sale_price" 
                                   name="sale_price" 
                                   value="{{ old('sale_price', $item->sale_price) }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="0.00"
                                   style="padding: 0.75rem 1rem; border-radius: 10px 0 0 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                                   oninput="calculateProfit()"
                                   required>
                            <span class="input-group-text" style="background: #f8fafc; border: 2px solid #e2e8f0; border-right: none; border-radius: 0 10px 10px 0; font-weight: 600; color: #64748b;">شيكل</span>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted" style="display: block; margin-top: 0.5rem;">
                            <i class="fas fa-cash-register"></i> سعر البيع للعميل
                        </small>
                    </div>
                </div>

                <!-- حساب الربح -->
                <div class="mt-4" style="padding: 1.25rem; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; border: 2px solid #bbf7d0;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 50px; height: 50px; background: #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-size: 0.9rem; color: #065f46; font-weight: 600;">الربح المتوقع</p>
                                    <p style="margin: 0; font-size: 1.8rem; font-weight: 700; color: #10b981;" id="profitAmount">0.00 شيكل</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <div style="display: inline-block; padding: 0.75rem 1.5rem; background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);">
                                <span style="font-size: 0.9rem; color: #065f46; font-weight: 600;">نسبة الربح: </span>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #10b981;" id="profitPercentage">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار الحفظ -->
            <div class="text-center">
                <div class="d-inline-flex gap-3">
                    <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-save"></i>
                        <span>حفظ التعديلات</span>
                    </button>
                    <a href="{{ route('catalog.index') }}" class="btn btn-lg" style="background: #64748b; color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                        <i class="fas fa-times"></i>
                        <span>إلغاء</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function calculateProfit() {
        const wholesalePrice = parseFloat(document.getElementById('wholesale_price').value) || 0;
        const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
        
        const profit = salePrice - wholesalePrice;
        const profitPercentage = wholesalePrice > 0 ? ((profit / wholesalePrice) * 100).toFixed(2) : 0;
        
        document.getElementById('profitAmount').textContent = profit.toFixed(2) + ' شيكل';
        document.getElementById('profitPercentage').textContent = profitPercentage + '%';
        
        // تغيير اللون بناءً على الربح
        const profitColor = profit >= 0 ? '#10b981' : '#ef4444';
        document.getElementById('profitAmount').style.color = profitColor;
        document.getElementById('profitPercentage').style.color = profitColor;
    }

    // حساب الربح عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        calculateProfit();
    });
</script>
@endpush

@push('styles')
<style>
    .form-control:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.15) !important;
    }

    .alert {
        animation: slideInDown 0.5s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .input-group:focus-within .input-group-text {
        border-color: #10b981;
    }
</style>
@endpush
@endsection