@extends('layout.app')

@section('title', 'تعديل الصيانة')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12" style="display:flex; align-items:center; gap:1rem;">
                <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
                <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                    <i class="fas fa-tools" style="color: #f59e0b; margin-right: 0.5rem;"></i>
                    تعديل الصيانة
                </h1>
            </div>
        </div>

        <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-body">
                <form action="{{ route('mobile-shop.maintenance.update', $maintenance) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label" style="font-weight: 600; color: #1e293b;">اسم الزبون</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   id="customer_name" name="customer_name" value="{{ old('customer_name', $maintenance->customer_name) }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label" style="font-weight: 600; color: #1e293b;">رقم الجوال</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" name="phone_number" value="{{ old('phone_number', $maintenance->phone_number) }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mobile_type" class="form-label" style="font-weight: 600; color: #1e293b;">نوع الجوال</label>
                            <input type="text" class="form-control @error('mobile_type') is-invalid @enderror" 
                                   id="mobile_type" name="mobile_type" value="{{ old('mobile_type', $maintenance->mobile_type) }}" required>
                            @error('mobile_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label" style="font-weight: 600; color: #1e293b;">طريقة الدفع</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                <option value="">اختر طريقة الدفع</option>
                                <option value="نقدي" {{ old('payment_method', $maintenance->payment_method) === 'نقدي' ? 'selected' : '' }}>نقدي</option>
                                <option value="تطبيق" {{ old('payment_method', $maintenance->payment_method) === 'تطبيق' ? 'selected' : '' }}>تطبيق</option>
                                <option value="مخ��لط" {{ old('payment_method', $maintenance->payment_method) === 'مختلط' ? 'selected' : '' }}>مختلط</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="problem_description" class="form-label" style="font-weight: 600; color: #1e293b;">مشكلة الجوال</label>
                        <textarea class="form-control @error('problem_description') is-invalid @enderror" 
                                  id="problem_description" name="problem_description" rows="4" required>{{ old('problem_description', $maintenance->problem_description) }}</textarea>
                        @error('problem_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cash_amount" class="form-label" style="font-weight: 600; color: #1e293b;">قيمة التكلفة نقدي</label>
                            <input type="number" class="form-control @error('cash_amount') is-invalid @enderror" 
                                   id="cash_amount" name="cash_amount" step="0.01" value="{{ old('cash_amount', $maintenance->cash_amount ?? 0) }}" required>
                            @error('cash_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_amount" class="form-label" style="font-weight: 600; color: #1e293b;">قيمة التكلفة بنكي</label>
                            <input type="number" class="form-control @error('bank_amount') is-invalid @enderror" 
                                   id="bank_amount" name="bank_amount" step="0.01" value="{{ old('bank_amount', $maintenance->bank_amount ?? 0) }}" required>
                            @error('bank_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="delivery_date" class="form-label" style="font-weight: 600; color: #1e293b;">تاريخ التسليم</label>
                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                   id="delivery_date" name="delivery_date" value="{{ old('delivery_date', $maintenance->delivery_date?->format('Y-m-d')) }}">
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="receipt_date" class="form-label" style="font-weight: 600; color: #1e293b;">تاريخ الاستلام</label>
                            <input type="date" class="form-control @error('receipt_date') is-invalid @enderror" 
                                   id="receipt_date" name="receipt_date" value="{{ old('receipt_date', $maintenance->receipt_date?->format('Y-m-d')) }}">
                            @error('receipt_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> تحديث
                        </button>
                        <a href="{{ route('mobile-shop.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
