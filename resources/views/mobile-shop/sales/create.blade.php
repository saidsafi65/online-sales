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
                {{-- Validation errors or general error messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('mobile-shop.sales.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label" style="font-weight: 600; color: #1e293b;">اسم المنتج</label>
                            <select class="form-select @error('product_name') is-invalid @enderror" 
                                    id="product_name" name="product_name" required>
                                <option value="">اختر المنتج</option>
                                @foreach (($products ?? collect())->keys() as $product)
                                    <option value="{{ $product }}" {{ old('product_name') == $product ? 'selected' : '' }}>
                                        {{ $product }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="product_type" class="form-label" style="font-weight: 600; color: #1e293b;">النوع / الموديل</label>
                            <select class="form-select @error('product_type') is-invalid @enderror" 
                                    id="product_type" name="product_type" required>
                                <option value="">اختر النوع / الموديل</option>
                            </select>
                            @error('product_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label" style="font-weight: 600; color: #1e293b;">الكمية</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                            <small class="text-muted" id="available_quantity"></small>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
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

                    <div class="row" id="payment_amounts">
                        <div class="col-md-6 mb-3" id="cash_amount_group">
                            <label for="cash_amount" class="form-label" style="font-weight: 600; color: #1e293b;">قيمة التكلفة نقدي</label>
                            <input type="number" class="form-control @error('cash_amount') is-invalid @enderror" 
                                   id="cash_amount" name="cash_amount" step="0.01" value="{{ old('cash_amount', 0) }}" required>
                            @error('cash_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3" id="bank_amount_group">
                            <label for="bank_amount" class="form-label" style="font-weight: 600; color: #1e293b;">قيمة التكلفة بنكي</label>
                            <input type="number" class="form-control @error('bank_amount') is-invalid @enderror" 
                                   id="bank_amount" name="bank_amount" step="0.01" value="{{ old('bank_amount', 0) }}" required>
                            @error('bank_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="created_at" class="form-label" style="font-weight: 600; color: #1e293b;">تاريخ المبيعة</label>
                            <input type="datetime-local" class="form-control @error('created_at') is-invalid @enderror"
                                   id="created_at" name="created_at" value="{{ old('created_at') }}">
                            <small class="text-muted">اتركه فارغاً لاستخدام تاريخ ووقت الآن تلقائياً</small>
                            @error('created_at')
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

@push('scripts')
<script>
(function() {
    const products = @json($products->map(function($arr){ return $arr->toArray(); })->toArray() ?? []);
    const inventory = @json($inventory ?? []);
    const productSelect = document.getElementById('product_name');
    const typeSelect = document.getElementById('product_type');
    const quantityInput = document.getElementById('quantity');
    const availableQtyText = document.getElementById('available_quantity');
    const paymentMethodSelect = document.getElementById('payment_method');
    const cashAmountGroup = document.getElementById('cash_amount_group');
    const bankAmountGroup = document.getElementById('bank_amount_group');
    const cashAmountInput = document.getElementById('cash_amount');
    const bankAmountInput = document.getElementById('bank_amount');

    function fillTypes(selectedProduct) {
        typeSelect.innerHTML = '<option value="">اختر النوع / الموديل</option>';
        availableQtyText.textContent = '';
        
        const types = products[selectedProduct] || [];
        if (!types || types.length === 0) {
            return;
        }

        types.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            if (t === @json(old('product_type'))) opt.selected = true;
            typeSelect.appendChild(opt);
        });
    }

    function updateAvailableQuantity() {
        const selectedProduct = productSelect.value;
        const selectedType = typeSelect.value;
        
        if (selectedProduct && selectedType) {
            const item = inventory.find(i => 
                i.product_name === selectedProduct && i.model_type === selectedType
            );
            
            if (item) {
                availableQtyText.textContent = `الكمية المتاحة: ${item.quantity}`;
                quantityInput.max = item.quantity;
            }
        }
    }

    function updatePaymentFields() {
        const method = paymentMethodSelect.value;
        switch (method) {
            case 'نقدي':
                cashAmountGroup.classList.remove('d-none');
                bankAmountGroup.classList.add('d-none');
                cashAmountInput.removeAttribute('readonly');
                bankAmountInput.value = '0';
                bankAmountInput.setAttribute('readonly', 'readonly');
                break;
            case 'تطبيق':
                bankAmountGroup.classList.remove('d-none');
                cashAmountGroup.classList.add('d-none');
                bankAmountInput.removeAttribute('readonly');
                cashAmountInput.value = '0';
                cashAmountInput.setAttribute('readonly', 'readonly');
                break;
            case 'مختلط':
                cashAmountGroup.classList.remove('d-none');
                bankAmountGroup.classList.remove('d-none');
                cashAmountInput.removeAttribute('readonly');
                bankAmountInput.removeAttribute('readonly');
                break;
            default:
                cashAmountGroup.classList.remove('d-none');
                bankAmountGroup.classList.remove('d-none');
        }
    }

    productSelect.addEventListener('change', function() {
        fillTypes(this.value);
        availableQtyText.textContent = '';
    });

    typeSelect.addEventListener('change', updateAvailableQuantity);
    paymentMethodSelect.addEventListener('change', updatePaymentFields);

    document.addEventListener('DOMContentLoaded', function() {
        const initial = productSelect.value || @json(old('product_name')) || '';
        if (initial) {
            productSelect.value = initial;
            fillTypes(initial);
            updateAvailableQuantity();
        }
        updatePaymentFields();
    });
})();
</script>
@endpush