@extends('layout.gust')

@section('title', 'تفاصيل الطلب')

@push('styles')
<style>
    .account-wrapper { padding: 1rem 0 3rem; }
    .account-title { font-size: 1.9rem; font-weight: 900; margin-bottom: .5rem; }
    .back-link { color: var(--text-secondary); text-decoration: none; font-size: .9rem; display: inline-block; margin-bottom: 1.5rem; }
    .detail-card { background: #fff; border-radius: 16px; padding: 1.8rem; box-shadow: var(--shadow-md); margin-bottom: 1.5rem; }
    .detail-card h5 { font-weight: 800; margin-bottom: 1.2rem; display: flex; align-items: center; gap: .5rem; }
    .detail-card h5 i { color: var(--primary-color); }
    .info-row { display: flex; justify-content: space-between; padding: .6rem 0; border-bottom: 1px solid #f1f5f9; font-size: .92rem; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-secondary); }
    .info-value { font-weight: 700; }
    .status-badge { padding: .3rem .8rem; border-radius: 50px; font-size: .78rem; font-weight: 700; }
    .status-pending { background: #fef3c7; color: #b45309; }
    .status-paid { background: #d1fae5; color: #047857; }
    .status-failed { background: #fee2e2; color: #b91c1c; }
    .status-cancelled { background: #e2e8f0; color: #475569; }
    .order-line { display: flex; justify-content: space-between; padding: .7rem 0; border-bottom: 1px solid #f1f5f9; font-size: .92rem; }
    .order-line:last-of-type { border-bottom: none; }
    .order-line-meta { color: var(--text-secondary); font-size: .82rem; }
    .order-total-row {
        display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 900;
        border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: .5rem;
    }
</style>
@endpush

@section('content')
<div class="account-wrapper">
    <a href="{{ route('customer.orders') }}" class="back-link">→ الرجوع لطلباتي</a>
    <h1 class="account-title">طلب #{{ $order->id }}</h1>

    <div class="row">
        <div class="col-lg-7">
            <div class="detail-card">
                <h5><i class="fas fa-map-marker-alt"></i> بيانات التوصيل</h5>
                <div class="info-row"><span class="info-label">الاسم</span><span class="info-value">{{ $order->customer_name }}</span></div>
                <div class="info-row"><span class="info-label">الهاتف</span><span class="info-value">{{ $order->customer_phone }}</span></div>
                <div class="info-row"><span class="info-label">العنوان</span><span class="info-value">{{ $order->shipping_address }}</span></div>
                @if ($order->shipping_city)
                    <div class="info-row"><span class="info-label">المدينة</span><span class="info-value">{{ $order->shipping_city }}</span></div>
                @endif
            </div>

            <div class="detail-card">
                <h5><i class="fas fa-box"></i> المنتجات</h5>
                @foreach ($order->items as $item)
                    <div class="order-line">
                        <div>
                            <div class="fw-bold">{{ $item->product_name }}</div>
                            <div class="order-line-meta">{{ $item->quantity }} × {{ number_format($item->price, 2) }} ₪</div>
                        </div>
                        <div class="fw-bold">{{ number_format($item->price * $item->quantity, 2) }} ₪</div>
                    </div>
                @endforeach

                <div class="order-total-row">
                    <span>الإجمالي</span>
                    <span>{{ number_format($order->total, 2) }} ₪</span>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="detail-card">
                <h5><i class="fas fa-truck"></i> حالة الطلب</h5>
                @include('customer.partials.order-status-tracker', ['order' => $order])
            </div>

            <div class="detail-card">
                <h5><i class="fas fa-credit-card"></i> الدفع</h5>
                <div class="info-row">
                    <span class="info-label">طريقة الدفع</span>
                    <span class="info-value">جوال باي</span>
                </div>
                @if ($order->payment && $order->payment->transaction_id)
                    <div class="info-row">
                        <span class="info-label">رقم العملية</span>
                        <span class="info-value">{{ $order->payment->transaction_id }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">تاريخ الطلب</span>
                    <span class="info-value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection