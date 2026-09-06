@extends('layout.app')

@section('title', 'طلب #' . $order->id)

@push('styles')
<style>
    .back-link { color: var(--text-secondary); text-decoration: none; font-size: .9rem; display: inline-block; margin-bottom: 1rem; }
    .order-header {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
    }
    .order-header h1 { font-size: 1.7rem; font-weight: 900; margin: 0; }
    .detail-card {
        background: white; border-radius: 16px; padding: 1.8rem;
        box-shadow: var(--shadow-md); margin-bottom: 1.5rem;
    }
    .detail-card h5 { font-weight: 800; margin-bottom: 1.2rem; display: flex; align-items: center; gap: .5rem; }
    .detail-card h5 i { color: var(--primary-color); }
    .info-row { display: flex; justify-content: space-between; padding: .6rem 0; border-bottom: 1px solid #f1f5f9; font-size: .92rem; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-secondary); }
    .info-value { font-weight: 700; }
    .status-badge { padding: .3rem .8rem; border-radius: 50px; font-size: .78rem; font-weight: 700; }
    .status-pending    { background: #fef3c7; color: #b45309; }
    .status-paid       { background: #d1fae5; color: #047857; }
    .status-processing { background: #dbeafe; color: #1d4ed8; }
    .status-shipped    { background: #e0e7ff; color: #4338ca; }
    .status-delivered  { background: #d1fae5; color: #047857; }
    .status-failed     { background: #fee2e2; color: #b91c1c; }
    .status-cancelled  { background: #e2e8f0; color: #475569; }
    .order-line { display: flex; justify-content: space-between; padding: .7rem 0; border-bottom: 1px solid #f1f5f9; font-size: .92rem; }
    .order-line:last-of-type { border-bottom: none; }
    .order-line-meta { color: var(--text-secondary); font-size: .82rem; }
    .order-total-row {
        display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 900;
        border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: .5rem;
    }
    .status-update-form select {
        border-radius: 10px; padding: .7rem 1rem; border: 1px solid var(--border-color);
        width: 100%; margin-bottom: 1rem; font-family: inherit;
    }
    .status-update-form button {
        width: 100%; padding: .8rem; background: var(--primary-color); color: white;
        border: none; border-radius: 10px; font-weight: 700;
    }
</style>
@endpush

@section('content')
<a href="{{ route('online-orders.index') }}" class="back-link">→ الرجوع لكل الطلبات</a>

<div class="order-header">
    <h1>طلب #{{ $order->id }}</h1>
    <span class="status-badge status-{{ $order->status }}">
        {{ $statusFlow[$order->status] ?? $order->status }}
    </span>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="detail-card">
            <h5><i class="fas fa-user"></i> بيانات العميل والتوصيل</h5>
            <div class="info-row"><span class="info-label">الاسم</span><span class="info-value">{{ $order->customer_name }}</span></div>
            <div class="info-row"><span class="info-label">الهاتف</span><span class="info-value">{{ $order->customer_phone }}</span></div>
            <div class="info-row"><span class="info-label">العنوان</span><span class="info-value">{{ $order->shipping_address }}</span></div>
            @if ($order->shipping_city)
                <div class="info-row"><span class="info-label">المدينة</span><span class="info-value">{{ $order->shipping_city }}</span></div>
            @endif
            @if ($order->customer)
                <div class="info-row">
                    <span class="info-label">حساب العميل</span>
                    <span class="info-value">{{ $order->customer->phone }} @if($order->customer->email) — {{ $order->customer->email }} @endif</span>
                </div>
            @endif
        </div>

        <div class="detail-card">
            <h5><i class="fas fa-box"></i> المنتجات</h5>
            @foreach ($order->items as $item)
                <div class="order-line">
                    <div>
                        <div class="fw-bold">{{ $item->product_name }}</div>
                        <div class="order-line-meta">
                            {{ $item->quantity }} × {{ number_format($item->price, 2) }} ₪
                            @if ($item->branch)
                                — فرع {{ $item->branch->name }}
                            @endif
                        </div>
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
            <h5><i class="fas fa-edit"></i> تحديث حالة الطلب</h5>
            <form class="status-update-form" method="POST" action="{{ route('online-orders.update-status', $order) }}">
                @csrf
                @method('PATCH')
                <select name="status">
                    @foreach ($statusFlow as $key => $label)
                        <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit"><i class="fas fa-save"></i> حفظ الحالة</button>
            </form>
        </div>

        <div class="detail-card">
            <h5><i class="fas fa-credit-card"></i> معلومات الدفع</h5>
            <div class="info-row">
                <span class="info-label">طريقة الدفع</span>
                <span class="info-value">جوال باي</span>
            </div>
            @if ($order->payment)
                <div class="info-row">
                    <span class="info-label">حالة الدفع</span>
                    <span class="info-value">{{ $order->payment->status }}</span>
                </div>
                @if ($order->payment->transaction_id)
                    <div class="info-row">
                        <span class="info-label">رقم العملية</span>
                        <span class="info-value">{{ $order->payment->transaction_id }}</span>
                    </div>
                @endif
            @else
                <div class="info-row">
                    <span class="info-label">حالة الدفع</span>
                    <span class="info-value text-secondary">لا يوجد سجل دفع بعد</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">تاريخ الطلب</span>
                <span class="info-value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection