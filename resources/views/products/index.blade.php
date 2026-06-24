@extends('layout.gust')

@section('title', 'منتجاتنا المميزة')

@push('styles')
<style>
    .products-wrapper {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
        min-height: calc(100vh - 200px);
    }

    .sidebar-categories {
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .categories-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .categories-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .categories-title i {
        color: #6366f1;
        font-size: 1.5rem;
    }

    .category-item {
        padding: 0.85rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        color: var(--text-secondary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .category-item:hover {
        background: #f8fafc;
        border-left-color: #6366f1;
        color: #6366f1;
    }

    .category-item.active {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left-color: #1e40af;
        color: #1e40af;
        font-weight: 700;
    }

    .category-item i {
        font-size: 1.1rem;
    }

    .category-count {
        margin-left: auto;
        background: #e2e8f0;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .category-item.active .category-count {
        background: #6366f1;
        color: white;
    }

    .main-content {
        display: flex;
        flex-direction: column;
    }

    .welcome-section {
        text-align: center;
        margin-bottom: 2rem;
        animation: fadeInDown 0.6s ease-out;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
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
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .search-box {
        flex: 1;
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

    .sort-select {
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .sort-select:hover,
    .sort-select:focus {
        border-color: #6366f1;
        outline: none;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .product-card {
        background: white;
        border-radius: 16px;
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
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        position: relative;
        overflow: hidden;
        height: 220px;
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
        font-size: 2.5rem;
    }

    .product-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        z-index: 5;
    }

    .product-badge.discount {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        top: auto;
        bottom: 0.75rem;
        left: 0.75rem;
    }

    .product-info {
        padding: 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        font-size: 0.8rem;
        color: #6366f1;
        font-weight: 600;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
    }

    .product-price-section {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 2px solid #f1f5f9;
    }

    .price-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 900;
        color: #10b981;
    }

    .product-original-price {
        font-size: 0.9rem;
        color: #94a3b8;
        text-decoration: line-through;
    }

    .discount-amount {
        font-size: 0.85rem;
        color: #f59e0b;
        font-weight: 600;
        margin-left: auto;
    }

    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        color: var(--text-secondary);
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        grid-column: 1 / -1;
    }

    .empty-state-icon {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        opacity: 0.5;
        color: #cbd5e1;
    }

    .pagination-section {
        margin-top: 2rem;
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

    @media (max-width: 1024px) {
        .products-wrapper {
            grid-template-columns: 200px 1fr;
            gap: 1.5rem;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .products-wrapper {
            grid-template-columns: 1fr;
        }

        .sidebar-categories {
            position: relative;
            top: auto;
        }

        .welcome-title {
            font-size: 1.75rem;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .filter-section {
            flex-direction: column;
        }

        .search-box {
            width: 100%;
        }

        .sort-select {
            width: 100%;
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

    <div class="products-wrapper">
        <!-- Sidebar Categories -->
        <aside class="sidebar-categories">
            <div class="categories-card">
                <div class="categories-title">
                    <i class="fas fa-filter"></i>
                    التصنيفات
                </div>
                
                <div class="category-item active" onclick="filterByCategory(event, 'all')">
                    <i class="fas fa-th"></i>
                    <span>جميع المنتجات</span>
                    <span class="category-count">{{ $products->total() }}</span>
                </div>

                @php
                    $categories = App\Models\Product::distinct()->pluck('category');
                @endphp

                @foreach($categories as $category)
                    @php
                        $count = App\Models\Product::where('category', $category)->count();
                    @endphp
                    <div class="category-item" onclick="filterByCategory(event, '{{ $category }}')">
                        <i class="fas fa-folder"></i>
                        <span>{{ $category }}</span>
                        <span class="category-count">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Search and Sort -->
            <div class="filter-section">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="ابحث عن منتج...">
                </div>
                <select class="sort-select" id="sortSelect">
                    <option value="latest">الأحدث أولاً</option>
                    <option value="price-low">السعر: من الأقل للأعلى</option>
                    <option value="price-high">السعر: من الأعلى للأقل</option>
                    <option value="discount">الأكثر خصماً</option>
                </select>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="product-grid" id="productsGrid">
                    @foreach ($products as $product)
                    <div class="product-card" 
                         data-name="{{ strtolower($product->name) }}" 
                         data-category="{{ $product->category }}"
                         data-price="{{ $product->price }}"
                         data-discount="{{ $product->discount }}"
                         data-product-id="{{ $product->id }}">
                        <div class="product-image">
                            @if($product->image && file_exists(storage_path('app/public/' . $product->image)))
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}"
                                     loading="lazy">
                            @else
                                <div class="product-image-placeholder">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                            
                            @if($product->discount > 0)
                                <div class="product-badge discount">
                                    <i class="fas fa-tag me-1"></i>
                                    خصم {{ $product->discount }}%
                                </div>
                            @endif
                        </div>
                        @if($product->is_out_of_stock)
    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.55); 
                display: flex; flex-direction: column; align-items: center; 
                justify-content: center; z-index: 5; gap: 0.5rem;">
        <i class="fas fa-ban" style="font-size: 2.5rem; color: #ef4444; 
                                      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));"></i>
        <span style="color: white; font-weight: 800; font-size: 1rem; 
                     background: #ef4444; padding: 0.4rem 1.25rem; 
                     border-radius: 20px; letter-spacing: 0.5px;
                     box-shadow: 0 2px 8px rgba(239,68,68,0.5);">
            نفدت الكمية
        </span>
    </div>
@endif
                        <div class="product-info">
                            <div class="product-category">{{ $product->category }}</div>
                            <h3 class="product-title">{{ $product->name }}</h3>
                            <div class="product-price-section">
                                <div class="price-info">
                                    @if($product->discount > 0)
                                        <span class="product-original-price">{{ number_format($product->price, 2) }}</span>
                                        <span class="product-price">{{ number_format($product->getFinalPrice(), 2) }} شيكل</span>
                                    @else
                                        <span class="product-price">{{ number_format($product->price, 2) }} شيكل</span>
                                    @endif
                                </div>
                                @if($product->discount > 0)
                                    <div class="discount-amount">
                                        توفير {{ number_format($product->getDiscountAmount(), 2) }} شيكل
                                    </div>
                                @endif
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
        </main>
    </div>
</div>

@push('scripts')
<script>
    let currentCategory = 'all';

    // Filter by category
    function filterByCategory(e, category) {
        e.preventDefault();
        currentCategory = category;
        const products = document.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('active');
        });
        e.target.closest('.category-item').classList.add('active');
        
        products.forEach(product => {
            const productCategory = product.getAttribute('data-category');
            const productName = product.getAttribute('data-name');
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            let show = (category === 'all' || productCategory === category) && 
                       productName.includes(searchTerm);
            
            if (show) {
                product.style.display = 'block';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            showEmptyMessage();
        } else {
            removeEmptyMessage();
        }
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const products = document.querySelectorAll('.product-card');
        let visibleCount = 0;
        
        products.forEach(product => {
            const productName = product.getAttribute('data-name');
            const productCategory = product.getAttribute('data-category');
            
            let show = productName.includes(searchTerm) && 
                       (currentCategory === 'all' || productCategory === currentCategory);
            
            if (show) {
                product.style.display = 'block';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });

        if (visibleCount === 0 && searchTerm.length > 0) {
            showEmptyMessage();
        } else {
            removeEmptyMessage();
        }
    });

    // Sort functionality
    document.getElementById('sortSelect').addEventListener('change', function(e) {
        const grid = document.getElementById('productsGrid');
        const products = Array.from(document.querySelectorAll('.product-card:not(.empty-message)'));
        
        products.sort((a, b) => {
            const sortType = e.target.value;
            
            if (sortType === 'latest') {
                return 0; // Keep original order
            } else if (sortType === 'price-low') {
                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            } else if (sortType === 'price-high') {
                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
            } else if (sortType === 'discount') {
                return parseFloat(b.dataset.discount) - parseFloat(a.dataset.discount);
            }
        });
        
        products.forEach(product => grid.appendChild(product));
    });

    function showEmptyMessage() {
        if (document.querySelector('.empty-message')) return;
        const grid = document.getElementById('productsGrid');
        const message = document.createElement('div');
        message.className = 'empty-message';
        message.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 2rem; color: #64748b;';
        message.innerHTML = '<i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i><p>لم يتم العثور على منتجات</p>';
        grid.appendChild(message);
    }

    function removeEmptyMessage() {
        const message = document.querySelector('.empty-message');
        if (message) message.remove();
    }
</script>

@endsection
