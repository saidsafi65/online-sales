@extends('layout.app')

@section('title', 'إضافة عملية شراء (يُضاف للكتالوج)')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">إضافة عملية شراء (يُضاف للكتالوج تلقائياً)</h3>
            <small class="text-muted">ملاحظة: هذه العملية ستُضاف إلى سجل المشتريات والكتالوج معاً</small>
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
            <form action="{{ route('purchases.store-catalog') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                
                <div class="alert alert-info">
                    <strong>معلومات الكتالوج:</strong> سيتم إضافة المنتج تلقائياً إلى الكتالوج أو تحديث الكمية إذا كان موجوداً
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الصنف / المنتج <span class="text-danger">*</span></label>
                        <input type="text" name="item" class="form-control" value="{{ old('item') }}" required>
                        <small class="text-muted">سيُستخدم كاسم المنتج في الكتالوج</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">النوع / الموديل <span class="text-danger">*</span></label>
                        <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
                        <small class="text-muted">سيُستخدم كنوع المنتج في الكتالوج</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الكمية <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" min="1" class="form-control" value="{{ old('quantity', 1) }}" required>
                        <small class="text-muted">ستُضاف إلى الكتالوج</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">اختر</option>
                            <option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>كاش</option>
                            <option value="app" {{ old('payment_method')=='app'?'selected':'' }}>تطبيق</option>
                            <option value="mixed" {{ old('payment_method')=='mixed'?'selected':'' }}>مختلط</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">مبلغ كاش <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount_cash" class="form-control" value="{{ old('amount_cash', 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">مبلغ بنك <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount_bank" class="form-control" value="{{ old('amount_bank', 0) }}" required>
                    </div>

                    <div class="col-12"><hr></div>
                    <div class="col-12"><h5>معلومات الكتالوج</h5></div>

                    <div class="col-md-6">
                        <label class="form-label">سعر الجملة (للوحدة) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', 0) }}" required>
                        <small class="text-muted">سعر بيع الجملة في الكتالوج</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">سعر البيع (للوحدة) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price', 0) }}" required>
                        <small class="text-muted">سعر البيع للمستهلك في الكتالوج</small>
                    </div>

                    <div class="col-12"><hr></div>
                    <div class="col-12"><h5>معلومات المورد</h5></div>

                    <div class="col-md-6">
                        <label class="form-label">التاريخ <span class="text-danger">*</span></label>
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

                    <div class="col-12"><hr></div>
                    <div class="col-12"><h5>حالة الإرجاع</h5></div>

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
                    <button class="btn btn-primary" type="submit">حفظ في المشتريات والكتالوج</button>
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