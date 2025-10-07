@extends('layout.app')

@section('title', 'تعديل عملية شراء')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header"><h3 class="mb-0">تعديل عملية شراء</h3></div>
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

            <form action="{{ route('purchases.update', $purchase) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الصنف</label>
                        <input type="text" name="item" class="form-control" value="{{ old('item', $purchase->item) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">النوع</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type', $purchase->type) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الكمية</label>
                        <input type="number" name="quantity" min="1" class="form-control" value="{{ old('quantity', $purchase->quantity) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">اختر</option>
                            <option value="cash" {{ old('payment_method', $purchase->payment_method)=='cash'?'selected':'' }}>كاش</option>
                            <option value="app" {{ old('payment_method', $purchase->payment_method)=='app'?'selected':'' }}>تطبيق</option>
                            <option value="mixed" {{ old('payment_method', $purchase->payment_method)=='mixed'?'selected':'' }}>مختلط</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">مبلغ كاش</label>
                        <input type="number" step="0.01" name="amount_cash" class="form-control" value="{{ old('amount_cash', $purchase->amount_cash ?? 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">مبلغ بنك</label>
                        <input type="number" step="0.01" name="amount_bank" class="form-control" value="{{ old('amount_bank', $purchase->amount_bank ?? 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الإجمالي</label>
                        <input type="text" id="total_amount" class="form-control" value="{{ number_format(($purchase->amount_cash ?? 0) + ($purchase->amount_bank ?? 0), 2) }}" readonly disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">التاريخ</label>
                        <input type="datetime-local" name="purchase_date" class="form-control" value="{{ old('purchase_date', optional($purchase->purchase_date)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">اسم المورد</label>
                        <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name', $purchase->supplier_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">رقم الجوال</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $purchase->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">صورة الهوية (اختياري)</label>
                        <input type="file" name="id_image" accept="image/*" class="form-control">
                        @if($purchase->id_image)
                            <div class="mt-2">
                                <a href="/{{ $purchase->id_image }}" target="_blank" class="btn btn-sm btn-outline-info">عرض الصورة الحالية</a>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">مرجع</label>
                        <select name="is_returned" id="is_returned" class="form-select">
                            <option value="0" {{ old('is_returned', (int)$purchase->is_returned)===0?'selected':'' }}>لا</option>
                            <option value="1" {{ old('is_returned', (int)$purchase->is_returned)===1?'selected':'' }}>نعم</option>
                        </select>
                    </div>

                    <div id="return_fields" class="row g-3 {{ old('is_returned', (int)$purchase->is_returned)===1 ? '' : 'd-none' }}">
                        <div class="col-md-6">
                            <label class="form-label">العطل</label>
                            <textarea name="issue" class="form-control" rows="2">{{ old('issue', $purchase->issue) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الارجاع</label>
                            <input type="datetime-local" name="return_date" class="form-control" value="{{ old('return_date', optional($purchase->return_date)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $purchase->notes) }}</textarea>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button class="btn btn-primary" type="submit">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const isReturnedSelect = document.getElementById('is_returned');
    const returnFields = document.getElementById('return_fields');
    const amountCash = document.querySelector('input[name="amount_cash"]');
    const amountBank = document.querySelector('input[name="amount_bank"]');
    const totalAmount = document.getElementById('total_amount');

    function updateReturnVisibility(){
        if (isReturnedSelect.value === '1') returnFields.classList.remove('d-none');
        else returnFields.classList.add('d-none');
    }

    function calculateTotal(){
        const cash = parseFloat(amountCash.value) || 0;
        const bank = parseFloat(amountBank.value) || 0;
        totalAmount.value = (cash + bank).toFixed(2);
    }

    isReturnedSelect.addEventListener('change', updateReturnVisibility);
    amountCash.addEventListener('input', calculateTotal);
    amountBank.addEventListener('input', calculateTotal);
    
    document.addEventListener('DOMContentLoaded', function() {
        updateReturnVisibility();
        calculateTotal();
    });
    
    updateReturnVisibility();
    calculateTotal();
})();
</script>
@endpush