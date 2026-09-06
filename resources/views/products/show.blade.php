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
        color: #991b1b;
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb-item a:hover {
        color: #b91c1c;
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
        color: #991b1b;
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
        color: #b91c1c;
        border: 2px solid #b91c1c;
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
        background: #b91c1c;
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

                    @php $avgRating = $product->averageRating(); $reviewsCount = $reviews->count(); @endphp
                    <div style="margin-bottom:1rem; display:flex; align-items:center; gap:.5rem;">
                        <div style="color:#f59e0b;">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= round($avgRating) ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <span style="color: var(--text-secondary); font-size:.9rem;">
                            @if ($reviewsCount > 0)
                                {{ $avgRating }} ({{ $reviewsCount }} {{ $reviewsCount == 1 ? 'مراجعة' : 'مراجعات' }})
                            @else
                                لا يوجد تقييمات بعد
                            @endif
                        </span>
                    </div>

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
                        @auth('customer')
                            <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                                @csrf
                                <button type="submit" class="btn-back-products" style="{{ $isWishlisted ? 'background:#b91c1c; color:#fff;' : '' }}">
                                    <i class="fa{{ $isWishlisted ? 's' : 'r' }} fa-heart"></i>
                                    <span>{{ $isWishlisted ? 'بالمفضلة' : 'أضف للمفضلة' }}</span>
                                </button>
                            </form>
                        @endauth
                        <a href="#" class="btn-contact">
                            <i class="fas fa-phone"></i>
                            <span>تواصل معنا</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="product-details-card" style="margin-top: 2rem; padding: 2rem;">
            <h3 class="description-title" style="margin-bottom:1.5rem;">
                <i class="fas fa-star"></i> المراجعات ({{ $reviews->count() }})
            </h3>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @auth('customer')
                @if ($canReview)
                    <form method="POST" action="{{ route('reviews.store', $product) }}" style="background:#f8fafc; border-radius:12px; padding:1.2rem; margin-bottom:1.5rem;">
                        @csrf
                        <div style="margin-bottom:.8rem; font-weight:700;">{{ $myReview ? 'عدّل تقييمك' : 'قيّم هالمنتج' }}</div>
                        <div class="star-rating-input" style="display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:.2rem; font-size:1.6rem; margin-bottom:.8rem;">
                            @for ($i = 5; $i >= 1; $i--)
                                <label style="cursor:pointer; color:#f59e0b;">
                                    <input type="radio" name="rating" value="{{ $i }}" style="display:none;" class="star-radio" {{ ($myReview->rating ?? 0) == $i ? 'checked' : '' }} required>
                                    <i class="{{ ($myReview->rating ?? 0) >= $i ? 'fas' : 'far' }} fa-star"></i>
                                </label>
                            @endfor
                        </div>
                        <textarea name="comment" class="form-control mb-2" rows="2" placeholder="اكتب رأيك بالمنتج (اختياري)">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">{{ $myReview ? 'تحديث التقييم' : 'إرسال التقييم' }}</button>
                    </form>
                @else
                    <div class="alert alert-info">اشترِ هالمنتج حتى تقدر تقيّمه.</div>
                @endif
            @endauth

            @forelse ($reviews as $review)
                <div style="border-bottom:1px solid #f1f5f9; padding:1rem 0;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-weight:700;">{{ $review->customer->name ?? 'زبون' }}</div>
                        <div style="color:#f59e0b; font-size:.9rem;">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                    </div>
                    @if ($review->comment)
                        <p style="color:var(--text-secondary); margin:.5rem 0 0;">{{ $review->comment }}</p>
                    @endif
                    <div style="font-size:.75rem; color:#94a3b8; margin-top:.3rem;">{{ $review->created_at->format('Y-m-d') }}</div>
                </div>
            @empty
                <p style="color:var(--text-secondary);">ما في مراجعات لهالمنتج لسا.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.star-rating-input').forEach(function (group) {
        const radios = group.querySelectorAll('.star-radio');
        const icons = group.querySelectorAll('label i');
        radios.forEach(function (radio, idx) {
            radio.addEventListener('change', function () {
                icons.forEach(function (icon, iconIdx) {
                    // radios/icons are in reverse order (5..1) matching the row-reverse layout
                    icon.className = (iconIdx <= idx ? 'fas' : 'far') + ' fa-star';
                });
            });
        });
    });
</script>
@endpush
@endsection