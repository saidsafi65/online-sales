@extends('layout.gust')

@php
    $gatewayLabel = $gatewayLabel ?? 'جوال باي';
    $gatewayIcon = $gatewayIcon ?? 'fa-mobile-alt';
    $resolveRoute = $resolveRoute ?? 'jawwalpay.mock.resolve';
@endphp

@section('title', 'الدفع - ' . $gatewayLabel)

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
    .mock-banner {
        background: #fef3c7;
        color: #92400e;
        border-radius: 10px;
        padding: .8rem 1rem;
        font-weight: 700;
        font-size: .9rem;
        margin-bottom: 1.5rem;
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
        background: #dbeafe;
        color: #2563eb;
    }
    .result-title { font-size: 1.5rem; font-weight: 900; margin-bottom: .6rem; }
    .result-desc { color: var(--text-secondary); margin-bottom: 1.5rem; }
    .order-ref {
        background: var(--light-bg);
        border-radius: 10px;
        padding: .8rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    .mock-actions { display: flex; gap: .8rem; flex-wrap: wrap; justify-content: center; }
    .mock-actions form { flex: 1; min-width: 160px; }
    .btn-mock {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: .9rem 1rem;
        font-weight: 700;
        cursor: pointer;
        color: #fff;
    }
    .btn-mock-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .btn-mock-failed { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }
</style>
@endpush

@section('content')
<div class="result-wrapper">
    <div class="result-card">
        <div class="mock-banner">🧪 وضع تجريبي — {{ $gatewayLabel }} الحقيقي غير مفعل بعد</div>

        <div class="result-icon"><i class="fas {{ $gatewayIcon }}"></i></div>
        <div class="result-title">إتمام الدفع عبر {{ $gatewayLabel }}</div>
        <div class="result-desc">هاي محاكاة لبوابة الدفع لحد ما تتفعل بوابة {{ $gatewayLabel }} الحقيقية</div>

        <div class="order-ref">
            رقم الطلب: #{{ $order->id }}<br>
            الإجمالي: {{ number_format($order->total, 2) }} شيكل
        </div>

        <div class="mock-actions">
            <form method="POST" action="{{ route($resolveRoute, $order) }}">
                @csrf
                <input type="hidden" name="result" value="success">
                <button type="submit" class="btn-mock btn-mock-success">
                    <i class="fas fa-check"></i> محاكاة دفع ناجح
                </button>
            </form>
            <form method="POST" action="{{ route($resolveRoute, $order) }}">
                @csrf
                <input type="hidden" name="result" value="failed">
                <button type="submit" class="btn-mock btn-mock-failed">
                    <i class="fas fa-times"></i> محاكاة فشل الدفع
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
