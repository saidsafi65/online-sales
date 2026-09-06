@extends('layout.gust')

@section('title', 'طلباتي')

@push('styles')
<style>
    .account-wrapper { padding: 1rem 0 3rem; }
    .account-title { font-size: 1.9rem; font-weight: 900; margin-bottom: 2rem; }
    .account-tabs { display: flex; gap: .6rem; flex-wrap: wrap; margin-bottom: 2rem; }
    .account-tab {
        padding: .6rem 1.3rem; border-radius: 50px; background: #fff;
        color: var(--text-primary); font-weight: 700; font-size: .9rem;
        text-decoration: none; box-shadow: var(--shadow-md);
        display: flex; align-items: center; gap: .5rem;
    }
    .account-tab.active { background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); color: #fff; }
    .order-mini-card {
        background: #fff; border-radius: 14px; padding: 1.1rem 1.3rem;
        box-shadow: var(--shadow-md); margin-bottom: .9rem;
        display: flex; justify-content: space-between; align-items: center;
        text-decoration: none; color: inherit;
    }
    .order-mini-card:hover { box-shadow: var(--shadow-lg); color: inherit; }
    .status-badge { padding: .3rem .8rem; border-radius: 50px; font-size: .78rem; font-weight: 700; }
    .status-pending { background: #fef3c7; color: #b45309; }
    .status-paid { background: #d1fae5; color: #047857; }
    .status-failed { background: #fee2e2; color: #b91c1c; }
    .status-cancelled { background: #e2e8f0; color: #475569; }
    .empty-state { text-align: center; padding: 3rem; background: #fff; border-radius: 16px; box-shadow: var(--shadow-md); }
    .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; }
</style>
@endpush

@section('content')
<div class="account-wrapper">
    <h1 class="account-title"><i class="fas fa-user-circle"></i> حسابي</h1>

    <div class="account-tabs">
        <a href="{{ route('customer.account') }}" class="account-tab"><i class="fas fa-home"></i> نظرة عامة</a>
        <a href="{{ route('customer.profile.edit') }}" class="account-tab"><i class="fas fa-user-edit"></i> بياناتي</a>
        <a href="{{ route('customer.orders') }}" class="account-tab active"><i class="fas fa-box"></i> طلباتي</a>
        <a href="{{ route('customer.wishlist') }}" class="account-tab"><i class="fas fa-heart"></i> المفضلة</a>
        <a href="{{ route('customer.password.edit') }}" class="account-tab"><i class="fas fa-lock"></i> كلمة المرور</a>
    </div>

    @forelse ($orders as $order)
        <a href="{{ route('customer.orders.show', $order) }}" class="order-mini-card">
            <div>
                <div class="fw-bold">طلب #{{ $order->id }}</div>
                <div class="text-secondary" style="font-size:.82rem;">{{ $order->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <div class="text-end" style="min-width:140px;">
                <div class="fw-bold mb-1">{{ number_format($order->total, 2) }} ₪</div>
                @include('customer.partials.order-status-tracker', ['order' => $order, 'compact' => true])
            </div>
        </a>
    @empty
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h5>ما في طلبات لسا</h5>
        </div>
    @endforelse

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection