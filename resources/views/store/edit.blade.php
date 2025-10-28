@extends('layout.app')

@section('content')
    <div class="container py-4" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-warning text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">تعديل المنتج</h3>
                            <a href="{{ route('store.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-right"></i> العودة للقائمة
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- عرض الأخطاء --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>يرجى تصحيح الأخطاء التالية:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('store.update', $item->id) }}" method="POST" id="editForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- اسم المنتج --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم المنتج <span class="text-danger">*</span></label>
                                    <input type="text" name="product_name"
                                        class="form-control @error('product_name') is-invalid @enderror"
                                        value="{{ old('product_name', $item->product_name) }}" required>
                                    @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- نوع المنتج --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع المنتج <span class="text-danger">*</span></label>
                                    <input type="text" name="product_type"
                                        class="form-control @error('product_type') is-invalid @enderror"
                                        value="{{ old('product_type', $item->product_type) }}" required>
                                    @error('product_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- الكمية --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الكمية <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        value="{{ old('quantity', $item->quantity) }}" min="0" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- اسم المورد --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">اسم المورد <span class="text-danger">*</span></label>
                                    <input type="text" name="supplier_name"
                                        class="form-control @error('supplier_name') is-invalid @enderror"
                                        value="{{ old('supplier_name', $item->supplier_name) }}" required>
                                    @error('supplier_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- سعر الجملة --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">سعر الجملة <span class="text-danger">*</span></label>
                                    <input type="number" name="wholesale_price" id="wholesale_price"
                                        class="form-control @error('wholesale_price') is-invalid @enderror"
                                        value="{{ old('wholesale_price', $item->wholesale_price) }}" min="0"
                                        step="0.01" required>
                                    @error('wholesale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- طريقة الدفع --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">طريقة الدفع <span class="text-danger">*</span></label>
                                    <select name="payment_method" id="payment_method"
                                        class="form-select @error('payment_method') is-invalid @enderror" required>
                                        <option value="نقدي"
                                            {{ old('payment_method', $item->payment_method) == 'نقدي' ? 'selected' : '' }}>
                                            نقدي</option>
                                        <option value="بنكي"
                                            {{ old('payment_method', $item->payment_method) == 'بنكي' ? 'selected' : '' }}>
                                            بنكي</option>
                                        <option value="مختلط"
                                            {{ old('payment_method', $item->payment_method) == 'مختلط' ? 'selected' : '' }}>
                                            مختلط</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- المبلغ النقدي --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المبلغ النقدي <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="cash_amount" id="cash_amount"
                                        class="form-control @error('cash_amount') is-invalid @enderror"
                                        value="{{ old('cash_amount', $item->cash_amount) }}" min="0" step="0.01"
                                        required>
                                    @error('cash_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- المبلغ البنكي --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">المبلغ البنكي <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="bank_amount" id="bank_amount"
                                        class="form-control @error('bank_amount') is-invalid @enderror"
                                        value="{{ old('bank_amount', $item->bank_amount) }}" min="0" step="0.01"
                                        required>
                                    @error('bank_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- تاريخ الإضافة --}}
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">تاريخ الإضافة <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date_added"
                                        class="form-control @error('date_added') is-invalid @enderror"
                                        value="{{ old('date_added', $item->date_added) }}" required>
                                    @error('date_added')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- معلومات إضافية --}}
                            <div class="alert alert-info">
                                <small>
                                    <strong>تاريخ الإنشاء:</strong> {{ $item->created_at->format('Y-m-d H:i') }}<br>
                                    <strong>آخر تحديث:</strong> {{ $item->updated_at->format('Y-m-d H:i') }}
                                </small>
                            </div>

                            {{-- أزرار الإجراءات --}}
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('store.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-warning text-white">
                                    <i class="fas fa-save"></i> تحديث
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border: none;
            border-radius: 15px;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .form-label {
            color: #2d3748;
        }

        .gap-2 {
            gap: 0.5rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = document.getElementById('payment_method');
            const wholesalePrice = document.getElementById('wholesale_price');
            const cashAmount = document.getElementById('cash_amount');
            const bankAmount = document.getElementById('bank_amount');

            // تحديث المبالغ عند تغيير طريقة الدفع
            paymentMethod.addEventListener('change', updateAmounts);
            wholesalePrice.addEventListener('input', updateAmounts);

            function updateAmounts() {
                const method = paymentMethod.value;
                const price = parseFloat(wholesalePrice.value) || 0;

                if (method === 'نقدي') {
                    cashAmount.value = price.toFixed(2);
                    bankAmount.value = '0.00';
                    cashAmount.readOnly = false;
                    bankAmount.readOnly = true;
                } else if (method === 'بنكي') {
                    cashAmount.value = '0.00';
                    bankAmount.value = price.toFixed(2);
                    cashAmount.readOnly = true;
                    bankAmount.readOnly = false;
                } else if (method === 'مختلط') {
                    cashAmount.readOnly = false;
                    bankAmount.readOnly = false;
                }
            }

            // تطبيق القواعد عند تحميل الصفحة
            updateAmounts();
        });
    </script>
@endsection
