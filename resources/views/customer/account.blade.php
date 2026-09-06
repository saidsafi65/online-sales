@extends('layout.gust')

@section('title', 'حسابي')

@push('styles')
<style>
    .account-wrapper { padding: 1rem 0 3rem; }
    .account-title {
        font-size: 1.9rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 2rem;
    }
    .account-tabs {
        display: flex;
        gap: .6rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }
    .account-tab {
        padding: .6rem 1.3rem;
        border-radius: 50px;
        background: #fff;
        color: var(--text-primary);
        font-weight: 700;
        font-size: .9rem;
        text-decoration: none;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .account-tab.active {
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff;
    }
    .welcome-card {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 1.5rem;
        border-right: 4px solid var(--primary-color);
    }
    .welcome-card h4 { font-weight: 800; margin-bottom: .3rem; }
    .welcome-card p { color: var(--text-secondary); margin: 0; }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: .7rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: .92rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-secondary); }
    .info-value { font-weight: 700; color: var(--text-primary); }
    .order-mini-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.1rem 1.3rem;
        box-shadow: var(--shadow-md);
        margin-bottom: .9rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
        color: inherit;
    }
    .order-mini-card:hover { box-shadow: var(--shadow-lg); color: inherit; }
    .status-badge {
        padding: .3rem .8rem;
        border-radius: 50px;
        font-size: .78rem;
        font-weight: 700;
    }
    .status-pending { background: #fef3c7; color: #b45309; }
    .status-paid { background: #d1fae5; color: #047857; }
    .status-failed { background: #fee2e2; color: #b91c1c; }
    .status-cancelled { background: #e2e8f0; color: #475569; }
</style>
@endpush

@section('content')
<div class="account-wrapper">
    <h1 class="account-title"><i class="fas fa-user-circle"></i> حسابي</h1>

    <div class="account-tabs">
        <a href="{{ route('customer.account') }}" class="account-tab active"><i class="fas fa-home"></i> نظرة عامة</a>
        <a href="{{ route('customer.profile.edit') }}" class="account-tab"><i class="fas fa-user-edit"></i> بياناتي</a>
        <a href="{{ route('customer.orders') }}" class="account-tab"><i class="fas fa-box"></i> طلباتي</a>
        <a href="{{ route('customer.wishlist') }}" class="account-tab"><i class="fas fa-heart"></i> المفضلة</a>
        <a href="{{ route('customer.password.edit') }}" class="account-tab"><i class="fas fa-lock"></i> كلمة المرور</a>
    </div>

    <div class="welcome-card">
        <h4>أهلاً {{ $customer->name }} 👋</h4>
        <p>من هون تقدر تدير بياناتك وتتابع طلباتك</p>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="welcome-card">
                <h4 style="font-size:1.1rem; margin-bottom:1rem;"><i class="fas fa-id-card text-danger"></i> بياناتك</h4>
                <div class="info-row">
                    <span class="info-label">الهاتف</span>
                    <span class="info-value">{{ $customer->phone }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">البريد الإلكتروني</span>
                    <span class="info-value">{{ $customer->email ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">العنوان</span>
                    <span class="info-value">{{ $customer->address ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <h4 style="font-size:1.1rem; margin-bottom:1rem;"><i class="fas fa-history text-danger"></i> آخر الطلبات</h4>

            @forelse ($recentOrders as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="order-mini-card">
                    <div>
                        <div class="fw-bold">طلب #{{ $order->id }}</div>
                        <div class="text-secondary" style="font-size:.82rem;">{{ $order->created_at->format('Y-m-d') }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold mb-1">{{ number_format($order->total, 2) }} ₪</div>
                        <span class="status-badge status-{{ $order->status }}">
                            @switch($order->status)
                                @case('paid') مدفوع @break
                                @case('pending') قيد المعالجة @break
                                @case('failed') فشل @break
                                @case('cancelled') ملغي @break
                            @endswitch
                        </span>
                    </div>
                </a>
            @empty
                <p class="text-secondary">ما في طلبات لسا</p>
            @endforelse
        </div>
    </div>
</div>
@endsection