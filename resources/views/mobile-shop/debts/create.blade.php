@extends('layout.app')

@section('title', 'إضافة دين')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12" style="display:flex; align-items:center; gap:1rem;">
                <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
                <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                    <i class="fas fa-hand-holding-usd" style="color: #ef4444; margin-right: 0.5rem;"></i>
                    إضافة دين
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

                <form method="POST" action="{{ route('mobile-shop.debts.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>اسم العميل</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>رقم الجوال</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>النوع</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>نقدي</label>
                            <input type="number" step="0.01" name="cash_amount" class="form-control" value="{{ old('cash_amount', 0) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>بنكي</label>
                            <input type="number" step="0.01" name="bank_amount" class="form-control" value="{{ old('bank_amount', 0) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>تاريخ الدين</label>
                            <input type="date" name="debt_date" class="form-control" value="{{ old('debt_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>تاريخ السداد (اختياري)</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date') }}">
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <a href="{{ route('mobile-shop.debts.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
