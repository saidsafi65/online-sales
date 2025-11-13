@extends('layout.app')

@section('title', 'تعديل المصروف')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12" style="display:flex; align-items:center; gap:1rem;">
                <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
                <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                    <i class="fas fa-receipt" style="color: #8b5cf6; margin-right: 0.5rem;"></i>
                    تعديل المصروف
                </h1>
            </div>
        </div>

        <div class="card">
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

                <form method="POST" action="{{ route('mobile-shop.expenses.update', $expense) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>الصنف</label>
                            <input type="text" name="category" class="form-control" value="{{ old('category', $expense->category) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>النوع</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type', $expense->type) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>الكمية</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $expense->quantity) }}" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label>طريقة الدفع</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="نقدي" {{ old('payment_method', $expense->payment_method)=='نقدي'?'selected':'' }}>نقدي</option>
                                <option value="بنكي" {{ old('payment_method', $expense->payment_method)=='بنكي'?'selected':'' }}>بنكي</option>
                                <option value="مختلط" {{ old('payment_method', $expense->payment_method)=='مختلط'?'selected':'' }}>مختلط</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>مبلغ كاش</label>
                            <input type="number" step="0.01" name="cash_amount" class="form-control" value="{{ old('cash_amount', $expense->cash_amount) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>مبلغ بنك</label>
                            <input type="number" step="0.01" name="bank_amount" class="form-control" value="{{ old('bank_amount', $expense->bank_amount) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label>التاريخ</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>اسم المورد</label>
                            <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name', $expense->supplier_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label>رقم الجوال</label>
                            <input type="text" name="supplier_phone" class="form-control" value="{{ old('supplier_phone', $expense->supplier_phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label>صورة الهوية (اختياري)</label>
                            <input type="file" name="id_photo" class="form-control">
                            @if($expense->id_photo)
                                <div class="mt-2"><a href="/{{ $expense->id_photo }}" target="_blank" class="btn btn-sm btn-outline-info">عرض الصورة الحالية</a></div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label>مرجع (اختياري)</label>
                            <input type="text" name="reference" class="form-control" value="{{ old('reference', $expense->reference) }}">
                        </div>
                        <div class="col-md-6">
                            <label>العطل (اختياري)</label>
                            <input type="text" name="defect" class="form-control" value="{{ old('defect', $expense->defect) }}">
                        </div>
                        <div class="col-md-6">
                            <label>تاريخ الإرجاع (اختياري)</label>
                            <input type="date" name="return_date" class="form-control" value="{{ old('return_date', optional($expense->return_date)->toDateString()) }}">
                        </div>
                        <div class="col-12">
                            <label>ملاحظات</label>
                            <textarea name="notes" class="form-control">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <a href="{{ route('mobile-shop.expenses.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
