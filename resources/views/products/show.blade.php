@extends('layout.gust')

@section('title', $product->name)

@push('styles')
<style>
    .product-container {
        max-width: 1200px;
        margin: 3rem auto;
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .breadcrumb-nav {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .breadcrumb {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .breadcrumb-item {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .breadcrumb-item a {
        color: #6366f1;
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb-item a:hover {
        color: #1e40af;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: '/';
        margin-left: 0.75rem;
        color: #cbd5e1;
    }

    .product-details-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .product-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .product-image-section {
        background: #f8fafc;
        padding: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .product-image-wrapper {
        width: 100%;
        max-width: 500px;
        position: relative;
    }

    .product-main-image {
        width: 100%;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .product-main-image:hover {
        transform: scale(1.05);
    }

    .product-badge-detail {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        padding: 0.6rem 1.5rem;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .product-content-section {
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .product-title-main {
        font-size: 2.25rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        line-height: 1.3;
    }

    .product-price-box {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 2px solid #10b981;
    }

    .price-label {
        color: #065f46;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .product-price-main {
        font-size: 2.5rem;
        font-weight: 900;
        color: #10b981;
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
    }

    .currency {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .product-description-box {
        margin-bottom: 2rem;
    }

    .description-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .description-title i {
        color: #6366f1;
    }

    .product-description-text {
        color: var(--text-secondary);
        line-height: 1.8;
        font-size: 1.05rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-back-products {
        flex: 1;
        background: white;
        color: #1e40af;
        border: 2px solid #1e40af;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        font-size: 1.05rem;
    }

    .btn-back-products:hover {
        background: #1e40af;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 64, 175, 0.3);
    }

    .btn-contact {
        flex: 1;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        font-size: 1.05rem;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-contact:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
    }

    @media (max-width: 991px) {
        .product-layout {
            grid-template-columns: 1fr;
        }

        .product-image-section {
            padding: 2rem;
        }

        .product-content-section {
            padding: 2rem;
        }

        .product-title-main {
            font-size: 1.75rem;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="product-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">المنتجات</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <!-- Product Details -->
        <div class="product-details-card">
            <div class="product-layout">
                <!-- Image Section -->
                <div class="product-image-section">
                    <div class="product-image-wrapper">
                        <div class="product-badge-detail">✨ متوفر الآن</div>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-main-image">
                        @else
                            <img src="https://via.placeholder.com/500/6366f1/ffffff?text=No+Image" alt="{{ $product->name }}" class="product-main-image">
                        @endif
                    </div>
                </div>

                <!-- Content Section -->
                <div class="product-content-section">
                    <h1 class="product-title-main">{{ $product->name }}</h1>

                    <!-- Price Box -->
                    <div class="product-price-box">
                        <div class="price-label">السعر</div>
                        <div class="product-price-main">
                            <span>{{ number_format($product->price, 2) }}</span>
                            <span class="currency">شيكل</span>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($product->description)
                        <div class="product-description-box">
                            <h3 class="description-title">
                                <i class="fas fa-info-circle"></i>
                                وصف المنتج
                            </h3>
                            <p class="product-description-text">{{ $product->description }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('products.index') }}" class="btn-back-products">
                            <i class="fas fa-arrow-right"></i>
                            <span>العودة للمنتجات</span>
                        </a>
                        <a href="#" class="btn-contact">
                            <i class="fas fa-phone"></i>
                            <span>تواصل معنا</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection