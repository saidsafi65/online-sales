@extends('layout.app')

@section('title', 'تعديل منتج')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4>تعديل المنتج</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('mobile-shop.inventory.update', $inventory) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>اسم المنتج</label>
                            <input type="text" name="product_name" class="form-control" value="{{ old('product_name', $inventory->product_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>النوع / الموديل</label>
                            <input type="text" name="model_type" class="form-control" value="{{ old('model_type', $inventory->model_type) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>الكمية</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $inventory->quantity) }}" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label>سعر الجملة</label>
                            <input type="number" step="0.01" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $inventory->wholesale_price) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>سعر البيع</label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', $inventory->selling_price) }}" required>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <a href="{{ route('mobile-shop.inventory.index') }}" class="btn btn-secondary">إلغاء</a>
                        <button class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
