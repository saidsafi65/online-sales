@extends('layout.app')

@section('title', 'تعديل الدين')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4>تعديل الدين</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('mobile-shop.debts.update', $debt) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>اسم العميل</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $debt->customer_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>رقم الجوال</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $debt->phone_number) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>النوع</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type', $debt->type) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>نقدي</label>
                            <input type="number" step="0.01" name="cash_amount" class="form-control" value="{{ old('cash_amount', $debt->cash_amount) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>بنكي</label>
                            <input type="number" step="0.01" name="bank_amount" class="form-control" value="{{ old('bank_amount', $debt->bank_amount) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>تاريخ الدين</label>
                            <input type="date" name="debt_date" class="form-control" value="{{ old('debt_date', $debt->debt_date->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>تاريخ السداد (اختياري)</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', optional($debt->payment_date)->toDateString()) }}">
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <a href="{{ route('mobile-shop.debts.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
