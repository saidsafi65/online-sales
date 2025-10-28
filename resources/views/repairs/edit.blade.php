@extends('layout.app')

@section('title', 'تعديل صيانة')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header"><h3 class="mb-0">تعديل صيانة #{{ $repair->id }}</h3></div>
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

            <form action="{{ route('repairs.update', $repair) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $repair->customer_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">اسم الجهاز</label>
                        <input type="text" name="device_name" class="form-control" value="{{ old('device_name', $repair->device_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الموديل</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model', $repair->model) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">رقم الجوال</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $repair->phone) }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">المشكلة / العطل</label>
                        <textarea name="issue" class="form-control" rows="3" required>{{ old('issue', $repair->issue) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تاريخ الاستلام</label>
                        <input type="datetime-local" name="received_date" class="form-control" value="{{ old('received_date', optional($repair->received_date)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">طريقة الدفع</label>
                        <select id="payment_method" name="payment_method" class="form-select" required>
                            <option value="">اختر</option>
                            <option value="cash" {{ old('payment_method', $repair->payment_method)=='cash'?'selected':'' }}>نقدي</option>
                            {{-- <option value="card" {{ old('payment_method', $repair->payment_method)=='card'?'selected':'' }}>بطاقة</option> --}}
                            <option value="app" {{ old('payment_method', $repair->payment_method)=='app'?'selected':'' }}>تطبيق</option>
                            <option value="mixed" {{ old('payment_method', $repair->payment_method)=='mixed'?'selected':'' }}>مختلط</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">التكلفة نقدي</label>
                        <input type="number" step="0.01" name="cost_cash" class="form-control" value="{{ old('cost_bank', $repair->cost_cash) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">التكلفة بنكي</label>
                        <input type="number" step="0.01" name="cost_bank" class="form-control" value="{{ old('cost_bank', $repair->cost_bank) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تاريخ التسليم</label>
                        <input type="datetime-local" name="delivery_date" class="form-control" value="{{ old('delivery_date', optional($repair->delivery_date)->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الموظف المستلم</label>
                        <input type="text" name="received_by" class="form-control" value="{{ old('received_by', $repair->received_by) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">مرجع</label>
                        <select name="is_returned" id="is_returned" class="form-select">
                            <option value="0" {{ old('is_returned', $repair->is_returned?'1':'0')==='0'?'selected':'' }}>لا</option>
                            <option value="1" {{ old('is_returned', $repair->is_returned?'1':'0')==='1'?'selected':'' }}>نعم</option>
                        </select>
                    </div>

                    <div id="return_fields" class="row g-3 d-none">
                        <div class="col-md-6">
                            <label class="form-label">سبب الارجاع</label>
                            <textarea name="return_reason" class="form-control" rows="2">{{ old('return_reason', $repair->return_reason) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الارجاع</label>
                            <input type="datetime-local" name="return_date" class="form-control" value="{{ old('return_date', optional($repair->return_date)->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التكلفة الارجاع</label>
                            <input type="number" step="0.01" name="return_cost" class="form-control" value="{{ old('return_cost', $repair->return_cost) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ تسليم المرجع</label>
                            <input type="datetime-local" name="return_delivery_date" class="form-control" value="{{ old('return_delivery_date', optional($repair->return_delivery_date)->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $repair->notes) }}</textarea>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('repairs.index') }}" class="btn btn-secondary">إلغاء</a>
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
