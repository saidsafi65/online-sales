@extends('layout.app')

@section('title', 'إضافة عملية شراء')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header"><h3 class="mb-0">إضافة عملية شراء</h3></div>
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

            <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الصنف</label>
                        <input type="text" name="item" class="form-control" value="{{ old('item') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">النوع</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الكمية</label>
                        <input type="number" name="quantity" min="1" class="form-control" value="{{ old('quantity', 1) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">اختر</option>
                            <option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>كاش</option>
                            <option value="app" {{ old('payment_method')=='app'?'selected':'' }}>تطبيق</option>
                            <option value="mixed" {{ old('payment_method')=='mixed'?'selected':'' }}>مختلط</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">المبلغ</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', 0) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">التاريخ</label>
                        <input type="datetime-local" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">اسم المورد</label>
                        <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">رقم الجوال</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">صورة الهوية (اختياري)</label>
                        <input type="file" name="id_image" accept="image/*" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">مرجع</label>
                        <select name="is_returned" id="is_returned" class="form-select">
                            <option value="0" {{ old('is_returned')==='0'?'selected':'' }}>لا</option>
                            <option value="1" {{ old('is_returned')==='1'?'selected':'' }}>نعم</option>
                        </select>
                    </div>

                    <div id="return_fields" class="row g-3 d-none">
                        <div class="col-md-6">
                            <label class="form-label">العطل</label>
                            <textarea name="issue" class="form-control" rows="2">{{ old('issue') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الارجاع</label>
                            <input type="datetime-local" name="return_date" class="form-control" value="{{ old('return_date') }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
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
    function updateReturnVisibility(){
        if (isReturnedSelect.value === '1') returnFields.classList.remove('d-none');
        else returnFields.classList.add('d-none');
    }
    isReturnedSelect.addEventListener('change', updateReturnVisibility);
    document.addEventListener('DOMContentLoaded', updateReturnVisibility);
    updateReturnVisibility();
})();
</script>
@endpush
