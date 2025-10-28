@extends('layout.app') 

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="mb-0">إضافة منتج جديد</h3>
                </div>
                <div class="card-body">
                    {{-- رسائل الخطأ --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('store.store') }}">
                        @csrf

                        <div class="row">
                            <!-- اسم المنتج -->
                            <div class="col-md-6 mb-3">
                                <label for="product_name">اسم المنتج</label>
                                <input type="text" name="product_name" class="form-control" value="{{ old('product_name') }}" required>
                            </div>

                            <!-- نوع المنتج -->
                            <div class="col-md-6 mb-3">
                                <label for="product_type">النوع</label>
                                <input type="text" name="product_type" class="form-control" value="{{ old('product_type') }}" required>
                            </div>

                            <!-- المورد -->
                            <div class="col-md-6 mb-3">
                                <label for="supplier_name">المورد</label>
                                <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}" required>
                            </div>

                            <!-- الكمية -->
                            <div class="col-md-6 mb-3">
                                <label for="quantity">الكمية</label>
                                <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
                            </div>

                            <!-- سعر الجملة -->
                            <div class="col-md-6 mb-3">
                                <label for="wholesale_price">سعر الجملة</label>
                                <input type="number" step="0.01" name="wholesale_price" class="form-control" value="{{ old('wholesale_price') }}" required>
                            </div>

                            <!-- طريقة الدفع -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_method">طريقة الدفع</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="نقدي" {{ old('payment_method') == 'نقدي' ? 'selected' : '' }}>نقدي</option>
                                    <option value="بنكي" {{ old('payment_method') == 'بنكي' ? 'selected' : '' }}>بنكي</option>
                                    <option value="مختلط" {{ old('payment_method') == 'مختلط' ? 'selected' : '' }}>مختلط</option>
                                </select>
                            </div>

                            <!-- المبلغ النقدي -->
                            <div class="col-md-6 mb-3">
                                <label for="cash_amount">المبلغ النقدي</label>
                                <input type="number" step="0.01" name="cash_amount" class="form-control" value="{{ old('cash_amount') }}">
                            </div>

                            <!-- المبلغ البنكي -->
                            <div class="col-md-6 mb-3">
                                <label for="bank_amount">المبلغ البنكي</label>
                                <input type="number" step="0.01" name="bank_amount" class="form-control" value="{{ old('bank_amount') }}">
                            </div>

                            <!-- تاريخ الإضافة -->
                            <div class="col-md-6 mb-3">
                                <label for="date_added">تاريخ الإضافة</label>
                                <input type="date" name="date_added" class="form-control" value="{{ old('date_added') }}" required>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">حفظ المنتج</button>
                            <a href="{{ route('store.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <style>
        .card {
            border: none;
            border-radius: 15px;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
@endsection
