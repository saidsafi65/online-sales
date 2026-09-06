@extends('layout.gust')

@section('title', 'المفضلة')

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
    .wishlist-card {
        background: #fff; border-radius: 16px; box-shadow: var(--shadow-md);
        overflow: hidden; text-decoration: none; color: inherit; display: block; height: 100%;
    }
    .wishlist-card:hover { box-shadow: var(--shadow-lg); color: inherit; }
    .wishlist-card img { width: 100%; height: 180px; object-fit: contain; background: #f8fafc; padding: 1rem; }
    .wishlist-card-body { padding: 1rem; }
    .wishlist-card-name { font-weight: 700; margin-bottom: .4rem; }
    .wishlist-card-price { color: var(--primary-color); font-weight: 800; }
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
        <a href="{{ route('customer.orders') }}" class="account-tab"><i class="fas fa-box"></i> طلباتي</a>
        <a href="{{ route('customer.wishlist') }}" class="account-tab active"><i class="fas fa-heart"></i> المفضلة</a>
        <a href="{{ route('customer.password.edit') }}" class="account-tab"><i class="fas fa-lock"></i> كلمة المرور</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($products->isEmpty())
        <div class="empty-state">
            <i class="fas fa-heart-crack"></i>
            <h5>مفضلتك فاضية</h5>
            <a href="{{ route('products.index') }}" class="btn btn-danger rounded-pill px-4 mt-2">تصفح المنتجات</a>
        </div>
    @else
        <div class="row g-3">
            @foreach ($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="wishlist-card">
                        <a href="{{ route('products.detail', $product) }}" style="text-decoration:none; color:inherit;">
                            <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png') }}" alt="{{ $product->name }}">
                            <div class="wishlist-card-body">
                                <div class="wishlist-card-name">{{ $product->name }}</div>
                                <div class="wishlist-card-price">{{ number_format($product->final_price, 2) }} ₪</div>
                            </div>
                        </a>
                        <div style="padding: 0 1rem 1rem;">
                            <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="fas fa-heart-crack"></i> إزالة من المفضلة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
