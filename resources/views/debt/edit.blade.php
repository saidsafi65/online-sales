@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>تعديل الدين</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('debts.update', $debt) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="customer_name" class="form-label">اسم العميل</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   id="customer_name" name="customer_name" 
                                   value="{{ old('customer_name', $debt->customer_name) }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الجوال</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $debt->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">النوع</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">اختر النوع</option>
                                <option value="دائن" {{ old('type', $debt->type) == 'دائن' ? 'selected' : '' }}>
                                    دائن (لي عنده)
                                </option>
                                <option value="مدين" {{ old('type', $debt->type) == 'مدين' ? 'selected' : '' }}>
                                    مدين (عليّ له)
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cash_amount" class="form-label">المبلغ النقدي</label>
                                <input type="number" step="0.01" class="form-control @error('cash_amount') is-invalid @enderror" 
                                       id="cash_amount" name="cash_amount" 
                                       value="{{ old('cash_amount', $debt->cash_amount) }}">
                                @error('cash_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bank_amount" class="form-label">المبلغ البنكي</label>
                                <input type="number" step="0.01" class="form-control @error('bank_amount') is-invalid @enderror" 
                                       id="bank_amount" name="bank_amount" 
                                       value="{{ old('bank_amount', $debt->bank_amount) }}">
                                @error('bank_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">سبب الدين</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="3" required>{{ old('reason', $debt->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="debt_date" class="form-label">تاريخ الدين</label>
                                <input type="date" class="form-control @error('debt_date') is-invalid @enderror" 
                                       id="debt_date" name="debt_date" 
                                       value="{{ old('debt_date', $debt->debt_date->format('Y-m-d')) }}" required>
                                @error('debt_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">تاريخ السداد (اختياري)</label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                       id="payment_date" name="payment_date" 
                                       value="{{ old('payment_date', $debt->payment_date?->format('Y-m-d')) }}">
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('debts.index') }}" class="btn btn-secondary">رجوع</a>
                            <button type="submit" class="btn btn-primary">تحديث</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection