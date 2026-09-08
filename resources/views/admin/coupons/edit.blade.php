@extends('layout.app')

@section('title', 'تعديل كود الخصم')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title"><i class="fas fa-tags"></i> تعديل كود الخصم</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('coupons.update', $coupon) }}">
                        @csrf
                        @method('PUT')
                        @include('admin.coupons._form', ['coupon' => $coupon])
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
