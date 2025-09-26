@extends('layout.app')

@section('title', 'إضافة عملية بيع جديدة')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">إضافة عملية بيع جديدة</h3>
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

            <form action="{{ route('sales.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product" class="form-label">اسم المنتج</label>
                        <input type="text" class="form-control @error('product') is-invalid @enderror"
                            id="product" name="product" value="{{ old('product') }}" required>
                        @error('product')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">النوع / الموديل</label>
                        <input type="text" class="form-control @error('type') is-invalid @enderror"
                            id="type" name="type" value="{{ old('type') }}" required>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="sale_date" class="form-label">تاريخ البيع</label>
                        <input type="datetime-local" class="form-control @error('sale_date') is-invalid @enderror"
                            id="sale_date" name="sale_date" value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('sale_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="form-label">طريقة الدفع</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror"
                            id="payment_method" name="payment_method" required>
                            <option value="">اختر طريقة الدفع</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                            <option value="app" {{ old('payment_method') == 'app' ? 'selected' : '' }}>تطبيق</option>
                            <option value="mixed" {{ old('payment_method') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cash_amount" class="form-label">المبلغ النقدي</label>
                        <input type="number" step="0.01" class="form-control @error('cash_amount') is-invalid @enderror"
                            id="cash_amount" name="cash_amount" value="{{ old('cash_amount', 0) }}" required>
                        @error('cash_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="app_amount" class="form-label">مبلغ التطبيق</label>
                        <input type="number" step="0.01" class="form-control @error('app_amount') is-invalid @enderror"
                            id="app_amount" name="app_amount" value="{{ old('app_amount', 0) }}" required>
                        @error('app_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                            id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('payment_method').addEventListener('change', function() {
    const cashInput = document.getElementById('cash_amount');
    const appInput = document.getElementById('app_amount');

    switch(this.value) {
        case 'cash':
            cashInput.removeAttribute('readonly');
            appInput.value = '0';
            appInput.setAttribute('readonly', 'readonly');
            break;
        case 'app':
        case 'card':
            cashInput.value = '0';
            cashInput.setAttribute('readonly', 'readonly');
            appInput.removeAttribute('readonly');
            break;
        case 'mixed':
            cashInput.removeAttribute('readonly');
            appInput.removeAttribute('readonly');
            break;
        default:
            cashInput.removeAttribute('readonly');
            appInput.removeAttribute('readonly');
    }
});
</script>
@endpush
