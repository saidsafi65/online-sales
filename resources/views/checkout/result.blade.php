@extends('layout.gust')

@section('title', 'حالة الطلب')

@push('styles')
<style>
    .result-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4rem 0;
    }
    .result-card {
        background: #fff;
        border-radius: 20px;
        padding: 3rem 2.5rem;
        text-align: center;
        max-width: 480px;
        width: 100%;
        box-shadow: var(--shadow-lg);
    }
    .result-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
    }
    .result-icon.success { background: #d1fae5; color: #10b981; }
    .result-icon.pending { background: #fef3c7; color: #f59e0b; }
    .result-icon.failed { background: #fee2e2; color: #ef4444; }
    .result-title { font-size: 1.5rem; font-weight: 900; margin-bottom: .6rem; }
    .result-desc { color: var(--text-secondary); margin-bottom: 2rem; }
    .order-ref {
        background: var(--light-bg);
        border-radius: 10px;
        padding: .8rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    .btn-back-products {
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: .8rem 2rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
    }
    .btn-back-products:hover { opacity: .92; color: #fff; }
</style>
@endpush

@section('content')
<div class="result-wrapper">
    <div class="result-card">
        @if ($order->status === 'paid')
            <div class="result-icon success"><i class="fas fa-check"></i></div>
            <div class="result-title">تم الدفع بنجاح</div>
            <div class="result-desc">شكراً لك، تم استلام طلبك وسيتم التواصل معك قريباً</div>
        @elseif ($order->status === 'failed')
            <div class="result-icon failed"><i class="fas fa-times"></i></div>
            <div class="result-title">فشلت عملية الدفع</div>
            <div class="result-desc">لم تتم عملية الدفع، يمكنك المحاولة مرة أخرى من صفحة السلة</div>
        @else
            <div class="result-icon pending"><i class="fas fa-clock"></i></div>
            <div class="result-title">طلبك قيد المعالجة</div>
            <div class="result-desc">سيتم تأكيد حالة الدفع خلال لحظات</div>
        @endif

        <div class="order-ref">رقم الطلب: #{{ $order->id }}</div>

        <a href="{{ route('products.index') }}" class="btn-back-products">
            <i class="fas fa-arrow-left"></i> الرجوع للمنتجات
        </a>
    </div>
</div>
@endsection