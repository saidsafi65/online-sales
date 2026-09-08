@extends('layout.gust')

@section('title', 'سلة المشتريات')

@push('styles')
<style>
    .cart-wrapper { padding: 1rem 0 3rem; }
    .cart-title {
        font-size: 1.9rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 2rem;
    }
    .cart-item-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }
    .cart-item-img {
        width: 90px;
        height: 90px;
        border-radius: 12px;
        object-fit: contain;
        background: #f8fafc;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name {
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--text-primary);
        margin-bottom: .3rem;
    }
    .cart-item-price {
        color: var(--primary-color);
        font-weight: 700;
        font-size: .95rem;
    }
    .qty-form {
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .qty-form input {
        width: 65px;
        text-align: center;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: .4rem;
    }
    .btn-qty-update {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: .4rem .8rem;
        font-size: .8rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .btn-remove-item {
        background: #fee2e2;
        color: var(--primary-color);
        border: none;
        border-radius: 8px;
        padding: .5rem .9rem;
        font-size: .85rem;
        font-weight: 700;
    }
    .cart-summary {
        background: #fff;
        border-radius: 16px;
        padding: 1.6rem;
        box-shadow: var(--shadow-md);
        border-top: 4px solid var(--primary-color);
        position: sticky;
        top: 100px;
    }
    .cart-summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: .8rem;
        font-size: .95rem;
        color: var(--text-secondary);
    }
    .cart-summary-discount-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: .8rem;
        font-size: .95rem;
        color: #059669;
        font-weight: 700;
    }
    .cart-summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.3rem;
        font-weight: 900;
        color: var(--text-primary);
        border-top: 1px solid #e2e8f0;
        padding-top: 1rem;
        margin-top: .5rem;
    }
    .cart-coupon-box { margin-bottom: 1rem; }
    .cart-coupon-box .input-group input { border-radius: 8px 0 0 8px; }
    .cart-coupon-box .input-group button { border-radius: 0 8px 8px 0; }
    .cart-coupon-applied {
        display: flex; justify-content: space-between; align-items: center;
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
        padding: .5rem .8rem; margin-bottom: 1rem; font-size: .88rem; color: #047857;
    }
    .cart-coupon-applied button { background: none; border: none; color: #dc2626; font-size: .8rem; cursor: pointer; }
    .btn-checkout {
        width: 100%;
        padding: .9rem;
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        margin-top: 1.2rem;
    }
    .btn-checkout:hover { opacity: .92; color: #fff; }
    .empty-cart {
        text-align: center;
        padding: 4rem 1rem;
        background: #fff;
        border-radius: 16px;
        box-shadow: var(--shadow-md);
    }
    .empty-cart i { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; }

    @media (max-width: 576px) {
        .cart-item-card { flex-wrap: wrap; gap: .8rem; padding: 1rem; }
        .cart-item-img { width: 64px; height: 64px; }
        .cart-item-info { flex: 1 1 calc(100% - 64px - .8rem); min-width: 0; }
        .qty-form { flex: 1 1 auto; }
        .cart-summary { position: static; top: auto; }
    }
</style>
@endpush

@section('content')
<div class="cart-wrapper">
    <h1 class="cart-title"><i class="fas fa-shopping-cart"></i> سلة المشتريات</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($cart->items->isEmpty())
        <div class="empty-cart">
            <i class="fas fa-shopping-basket"></i>
            <h4 class="mb-3">سلتك فارغة</h4>
            <a href="{{ route('products.index') }}" class="btn btn-danger px-4 py-2 rounded-pill fw-bold">
                تصفح المنتجات
            </a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                @foreach ($cart->items as $item)
                    <div class="cart-item-card">
                        <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : asset('images/placeholder.png') }}"
                             alt="{{ $item->product->name }}" class="cart-item-img">

                        <div class="cart-item-info">
                            <div class="cart-item-name">{{ $item->product->name }}</div>
                            <div class="cart-item-price">{{ number_format($item->price, 2) }} ₪ / للقطعة</div>
                        </div>

                        <form class="qty-form" method="POST" action="{{ route('cart.update', $item) }}">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1">
                            <button type="submit" class="btn-qty-update">تحديث</button>
                        </form>

                        <div class="text-center" style="min-width:90px;">
                            <div class="fw-bold mb-2">{{ number_format($item->subtotal, 2) }} ₪</div>
                            <form method="POST" action="{{ route('cart.remove', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-remove-item">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    @if ($cart->applied_coupon)
                        <div class="cart-coupon-applied">
                            <span><i class="fas fa-check-circle"></i> كود "{{ $cart->coupon_code }}" مطبّق</span>
                            <form method="POST" action="{{ route('cart.coupon.remove') }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit">إلغاء</button>
                            </form>
                        </div>
                    @else
                        <div class="cart-coupon-box">
                            <form method="POST" action="{{ route('cart.coupon.apply') }}" class="input-group">
                                @csrf
                                <input type="text" name="code" class="form-control" placeholder="كود الخصم" required>
                                <button type="submit" class="btn btn-outline-secondary">تطبيق</button>
                            </form>
                        </div>
                    @endif

                    <div class="cart-summary-row">
                        <span>عدد القطع</span>
                        <span>{{ $cart->items->sum('quantity') }}</span>
                    </div>
                    <div class="cart-summary-row">
                        <span>المجموع الفرعي</span>
                        <span>{{ number_format($cart->total, 2) }} ₪</span>
                    </div>
                    @if ($cart->discount > 0)
                        <div class="cart-summary-discount-row">
                            <span>الخصم</span>
                            <span>- {{ number_format($cart->discount, 2) }} ₪</span>
                        </div>
                    @endif
                    <div class="cart-summary-total">
                        <span>الإجمالي</span>
                        <span>{{ number_format($cart->grand_total, 2) }} ₪</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-checkout d-block text-center text-decoration-none">
                        متابعة الدفع <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection