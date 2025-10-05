@extends('layout.app')

@section('title', 'إضافة عنصر كتالوج')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header"><h3 class="mb-0">إضافة عنصر إلى الكتالوج</h3></div>
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

            <form action="{{ route('catalog.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">اسم المنتج</label>
                    <input type="text" name="product" class="form-control" value="{{ old('product') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">النوع / الموديل</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الكمية</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">سعر الجملة</label>
                    <input type="number" name="wholesale_price" class="form-control" value="{{ old('wholesale_price') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">سعر البيع</label>
                    <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price') }}" required>
                </div>
                <div class="col-12 text-end">
                    <a href="{{ route('catalog.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button class="btn btn-primary" type="submit">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
