@extends('layout.app')

@section('title', 'تعديل عملية بيع')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">تعديل عملية بيع #{{ $sale->id }}</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sales.update', $sale->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product" class="form-label">اسم المنتج</label>
                        <input type="text" class="form-control @error('product') is-invalid @enderror"
                            id="product" name="product" value="{{ old('product', $sale->product) }}" required>
                        @error('product')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">النوع / الموديل</label>
                        <input type="text" class="form-control @error('type') is-invalid @enderror"
                            id="type" name="type" value="{{ old('type', $sale->type) }}" required>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="sale_date" class="form-label">تاريخ البيع</label>
                        <input type="datetime-local" class="form-control @error('sale_date') is-invalid @enderror"
                            id="sale_date" name="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d\TH:i')) }}" required>
                        @error('sale_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="form-label">طريقة الدفع</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror"
                            id="payment_method" name="payment_method" required>
                            <option value="">اختر طريقة الدفع</option>
                            <option value="cash" {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="card" {{ old('payment_method', $sale->payment_method) == 'card' ? 'selected' : '' }}>بطاقة</option>
                            <option value="app" {{ old('payment_method', $sale->payment_method) == 'app' ? 'selected' : '' }}>تطبيق</option>
                            <option value="mixed" {{ old('payment_method', $sale->payment_method) == 'mixed' ? 'selected' : '' }}>مختلط</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="cash_amount_group" class="col-md-6 mb-3">
                        <label for="cash_amount" class="form-label">المبلغ النقدي</label>
                        <input type="number" step="0.01" class="form-control @error('cash_amount') is-invalid @enderror"
                            id="cash_amount" name="cash_amount" value="{{ old('cash_amount', $sale->cash_amount) }}" required>
                        @error('cash_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="app_amount_group" class="col-md-6 mb-3">
                        <label for="app_amount" class="form-label">مبلغ التطبيق</label>
                        <input type="number" step="0.01" class="form-control @error('app_amount') is-invalid @enderror"
                            id="app_amount" name="app_amount" value="{{ old('app_amount', $sale->app_amount) }}" required>
                        @error('app_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                            id="notes" name="notes" rows="3">{{ old('notes', $sale->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const paymentSelect = document.getElementById('payment_method');
    const cashInput = document.getElementById('cash_amount');
    const appInput = document.getElementById('app_amount');
    const cashGroup = document.getElementById('cash_amount_group');
    const appGroup = document.getElementById('app_amount_group');

    function show(el) { el.classList.remove('d-none'); }
    function hide(el) { el.classList.add('d-none'); }

    function updateAmountFields() {
        const method = paymentSelect.value;
        switch (method) {
            case 'cash':
                show(cashGroup);
                hide(appGroup);
                cashInput.removeAttribute('readonly');
                appInput.value = '0';
                appInput.setAttribute('readonly', 'readonly');
                break;
            case 'app':
            case 'card':
                show(appGroup);
                hide(cashGroup);
                appInput.removeAttribute('readonly');
                cashInput.value = '0';
                cashInput.setAttribute('readonly', 'readonly');
                break;
            case 'mixed':
                show(cashGroup);
                show(appGroup);
                cashInput.removeAttribute('readonly');
                appInput.removeAttribute('readonly');
                break;
            default:
                show(cashGroup);
                show(appGroup);
                cashInput.removeAttribute('readonly');
                appInput.removeAttribute('readonly');
        }
    }

    paymentSelect.addEventListener('change', updateAmountFields);
    document.addEventListener('DOMContentLoaded', updateAmountFields);
    updateAmountFields();
})();
</script>
@endpush
