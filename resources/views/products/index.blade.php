@extends('layout.gust')

@section('title', 'منتجاتنا المميزة')

@push('styles')
<style>
    .welcome-section {
        text-align: center;
        margin-bottom: 3rem;
        animation: fadeInDown 0.6s ease-out;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .filter-section {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding: 0.875rem 1rem 0.875rem 3rem;
        border-radius: 50px;
        border: 2px solid #e2e8f0;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .search-box input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .search-box i {
        position: absolute;
        right: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 1.1rem;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .product-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1e40af 0%, #6366f1 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
        z-index: 10;
    }

    .product-card:hover::before {
        transform: scaleX(1);
    }

    .product-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        position: relative;
        overflow: hidden;
        height: 280px;
        background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    .product-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #6366f1;
        font-size: 3rem;
    }

    .product-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        z-index: 5;
    }

    .product-info {
        padding: 1.75rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-description {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: auto;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.25rem;
        margin-top: 1rem;
        border-top: 2px solid #f1f5f9;
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: 900;
        color: #10b981;
    }

    .btn-details {
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        color: white;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .btn-details:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        color: var(--text-secondary);
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .empty-state-icon {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        opacity: 0.5;
        color: #cbd5e1;
    }

    .pagination-section {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: 1fr;
        }

        .welcome-title {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="welcome-title">🛍️ منتجاتنا المميزة</h1>
        <p class="welcome-subtitle">اكتشف أحدث المنتجات بأفضل الأسعار</p>
    </div>

    <!-- Search/Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="ابحث عن منتج...">
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="product-grid" id="productsGrid">
            @foreach ($products as $product)
            <div class="product-card" data-name="{{ strtolower($product->name) }}" data-product-id="{{ $product->id }}">
                <div class="product-image">
                    @if($product->image && file_exists(storage_path('app/public/' . $product->image)))
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}"
                             loading="lazy">
                    @else
                        <div class="product-image-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                    <div class="product-badge">
                        <i class="fas fa-star me-1"></i>
                        جديد
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">{{ $product->name }}</h3>
                    @if($product->description)
                        <p class="product-description">{{ $product->description }}</p>
                    @endif
                    <div class="product-footer">
                        <div class="product-price">{{ number_format($product->price, 2) }} شيكل</div>
                        <a href="{{ route('products.show', $product->id) }}" class="btn-details">
                            <span>التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="pagination-section">
            {{ $products->links() }}
        </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-box-open"></i>
            </div>
            <h3>لا توجد منتجات متاحة</h3>
            <p>يرجى المحاولة لاحقاً</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const products = document.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        products.forEach(product => {
            const productName = product.getAttribute('data-name');
            if (productName.includes(searchTerm)) {
                product.style.display = 'block';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });

        // إظهار رسالة إذا لم توجد نتائج
        const grid = document.getElementById('productsGrid');
        if (visibleCount === 0 && searchTerm.length > 0) {
            grid.innerHTML += '<div style="grid-column: 1/-1; text-align: center; padding: 2rem; color: #64748b;"><i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i><p>لم يتم العثور على منتجات</p></div>';
        }
    });
</script>
@endpush
@endsection