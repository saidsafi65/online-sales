@extends('layout.gust')

@section('title', 'منتجاتنا')

@push('styles')
<style>
/* ===== RESET =====*/
*, *::before, *::after { box-sizing: border-box; }

:root {
    --brand:        #4f46e5;
    --brand-light:  #e0e7ff;
    --brand-dark:   #3730a3;
    --green:        #059669;
    --green-light:  #d1fae5;
    --amber:        #d97706;
    --amber-light:  #fef3c7;
    --red:          #dc2626;
    --slate-50:     #f8fafc;
    --slate-100:    #f1f5f9;
    --slate-200:    #e2e8f0;
    --slate-300:    #cbd5e1;
    --slate-400:    #94a3b8;
    --slate-500:    #64748b;
    --slate-700:    #334155;
    --slate-900:    #0f172a;
    --r-sm:         8px;
    --r-md:         14px;
    --r-lg:         20px;
    --r-xl:         28px;
    --sh-sm:        0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
    --sh-md:        0 4px 16px rgba(0,0,0,.08);
    --sh-lg:        0 12px 36px rgba(0,0,0,.12);
    --sh-xl:        0 24px 60px rgba(0,0,0,.18);
    --t:            0.22s cubic-bezier(.4,0,.2,1);
}

/* ===== LAYOUT ===== */
.shop-layout {
    display: grid;
    grid-template-columns: 270px 1fr;
    gap: 1.75rem;
    align-items: start;
    padding-bottom: 3rem;
}

/* ===== HEADER ===== */
.shop-header {
    margin-bottom: 1.75rem;
    padding: 2rem 2.25rem;
    border-radius: var(--r-xl);
    background: linear-gradient(135deg,#1e1b4b 0%,#312e81 45%,#4338ca 100%);
    color: white;
    position: relative;
    overflow: hidden;
}
.shop-header::before {
    content:'';position:absolute;inset:0;
    background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='30' cy='30' r='20' fill='%23ffffff' fill-opacity='0.04'/%3E%3C/svg%3E");
    pointer-events:none;
}
.shop-header-inner { position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem; }
.shop-header h1 { font-size:1.9rem;font-weight:900;margin:0;letter-spacing:-0.5px; }
.shop-header p  { font-size:1rem;opacity:.75;margin:.35rem 0 0; }
.header-stats   { display:flex;gap:1rem; }
.stat-pill {
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);
    border-radius:50px;padding:.45rem 1.1rem;font-size:.85rem;font-weight:600;
    backdrop-filter:blur(6px);display:flex;align-items:center;gap:.5rem;
}

/* ===== SIDEBAR ===== */
.sidebar { position:sticky;top:1.5rem;display:flex;flex-direction:column;gap:1rem; }

.filter-card { background:white;border-radius:var(--r-lg);box-shadow:var(--sh-md);overflow:hidden; }
.filter-card-header {
    padding:.85rem 1.2rem;background:var(--slate-50);border-bottom:1px solid var(--slate-200);
    font-size:.78rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;
    letter-spacing:.8px;display:flex;align-items:center;gap:.5rem;
}
.filter-card-body { padding:1rem 1.1rem; }

/* ===== CATEGORY LIST ===== */
.cat-list { display:flex;flex-direction:column;gap:.2rem; }
.cat-btn {
    display:flex;align-items:center;gap:.65rem;padding:.65rem .85rem;
    border-radius:var(--r-sm);cursor:pointer;border:none;background:transparent;
    width:100%;text-align:right;color:var(--slate-700);font-size:.92rem;font-weight:500;
    font-family:inherit;transition:background var(--t),color var(--t);
}
.cat-btn:hover { background:var(--slate-50);color:var(--brand); }
.cat-btn.active { background:var(--brand-light);color:var(--brand-dark);font-weight:700; }
.cat-icon {
    width:28px;height:28px;border-radius:var(--r-sm);background:var(--slate-100);
    display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;
    transition:background var(--t);
}
.cat-btn.active .cat-icon { background:var(--brand);color:white; }
.cat-label { flex:1; }
.cat-count {
    background:var(--slate-100);color:var(--slate-500);font-size:.73rem;font-weight:700;
    padding:.18rem .55rem;border-radius:50px;transition:all var(--t);
}
.cat-btn.active .cat-count { background:var(--brand);color:white; }

/* ===== PRICE RANGE ===== */
.price-range-wrap { display:flex;flex-direction:column;gap:.85rem; }
.price-inputs { display:grid;grid-template-columns:1fr 1fr;gap:.6rem; }
.price-input-group label { display:block;font-size:.73rem;font-weight:600;color:var(--slate-400);margin-bottom:.28rem; }
.price-input-group input {
    width:100%;padding:.5rem .65rem;border:1.5px solid var(--slate-200);border-radius:var(--r-sm);
    font-size:.88rem;font-family:inherit;color:var(--slate-700);font-weight:600;
    transition:border-color var(--t);
}
.price-input-group input:focus { outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.price-slider-track { position:relative;height:4px;background:var(--slate-200);border-radius:50px;margin:.5rem 0; }
.price-slider-fill  { position:absolute;height:100%;background:var(--brand);border-radius:50px; }
.price-slider {
    position:absolute;top:50%;transform:translateY(-50%);
    width:100%;height:4px;-webkit-appearance:none;appearance:none;background:transparent;cursor:pointer;
}
.price-slider::-webkit-slider-thumb {
    -webkit-appearance:none;width:18px;height:18px;border-radius:50%;background:var(--brand);
    border:2.5px solid white;box-shadow:0 2px 8px rgba(79,70,229,.4);cursor:pointer;position:relative;z-index:2;
}
.apply-price-btn {
    width:100%;padding:.62rem;border:none;border-radius:var(--r-sm);background:var(--brand);
    color:white;font-family:inherit;font-weight:700;font-size:.88rem;cursor:pointer;
    transition:background var(--t),transform var(--t);
}
.apply-price-btn:hover { background:var(--brand-dark);transform:translateY(-1px); }

/* ===== TOGGLES ===== */
.toggle-row { display:flex;align-items:center;justify-content:space-between;padding:.6rem 0; }
.toggle-label { font-size:.88rem;font-weight:600;color:var(--slate-700); }
.toggle-switch { position:relative;width:42px;height:24px;flex-shrink:0; }
.toggle-switch input { opacity:0;width:0;height:0; }
.toggle-slider {
    position:absolute;inset:0;background:var(--slate-200);border-radius:50px;cursor:pointer;transition:background var(--t);
}
.toggle-slider::before {
    content:'';position:absolute;width:18px;height:18px;left:3px;top:3px;background:white;
    border-radius:50%;transition:transform var(--t);box-shadow:0 1px 4px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-slider { background:var(--brand); }
.toggle-switch input:checked + .toggle-slider::before { transform:translateX(18px); }

/* ===== CLEAR BTN ===== */
.clear-btn {
    display:flex;align-items:center;justify-content:center;gap:.5rem;
    width:100%;padding:.65rem;border:1.5px solid var(--slate-200);border-radius:var(--r-sm);
    background:white;color:var(--slate-500);font-family:inherit;font-weight:600;
    font-size:.88rem;cursor:pointer;text-decoration:none;transition:all var(--t);
}
.clear-btn:hover { border-color:var(--red);color:var(--red);background:#fff5f5; }

/* ===== MAIN ===== */
.main-col { display:flex;flex-direction:column;gap:1.2rem; }

/* ===== TOOLBAR ===== */
.toolbar {
    background:white;border-radius:var(--r-lg);box-shadow:var(--sh-sm);
    padding:1rem 1.2rem;display:flex;gap:.85rem;align-items:center;
    border:1px solid var(--slate-200);flex-wrap:wrap;
}
.search-wrap { flex:1;min-width:180px;position:relative; }
.search-wrap input {
    width:100%;padding:.72rem 1rem .72rem 2.6rem;border:1.5px solid var(--slate-200);
    border-radius:var(--r-md);font-family:inherit;font-size:.93rem;color:var(--slate-700);
    background:var(--slate-50);transition:border-color var(--t),box-shadow var(--t);
}
.search-wrap input::placeholder { color:var(--slate-400); }
.search-wrap input:focus { outline:none;border-color:var(--brand);background:white;box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.search-icon { position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--slate-400);pointer-events:none; }
.sort-select {
    padding:.72rem 1rem;border:1.5px solid var(--slate-200);border-radius:var(--r-md);
    background:var(--slate-50);font-family:inherit;font-size:.88rem;color:var(--slate-700);
    font-weight:600;cursor:pointer;transition:border-color var(--t);
}
.sort-select:focus { outline:none;border-color:var(--brand); }
.results-count { font-size:.84rem;color:var(--slate-400);font-weight:600;white-space:nowrap; }
.results-count strong { color:var(--brand); }

/* ===== ACTIVE CHIPS ===== */
.active-filters { display:flex;flex-wrap:wrap;gap:.45rem;align-items:center; }
.filter-chip {
    display:inline-flex;align-items:center;gap:.4rem;background:var(--brand-light);
    color:var(--brand-dark);border-radius:50px;padding:.28rem .8rem;font-size:.8rem;
    font-weight:700;text-decoration:none;border:1.5px solid #c7d2fe;transition:all var(--t);
}
.filter-chip:hover { background:#c7d2fe; }

/* ===== PRODUCT GRID ===== */
.product-grid {
    display:grid;grid-template-columns:repeat(auto-fill,minmax(225px,1fr));gap:1.2rem;
}

/* ===== PRODUCT CARD ===== */
.product-card {
    background:white;border-radius:var(--r-lg);box-shadow:var(--sh-sm);
    border:1px solid var(--slate-200);overflow:hidden;display:flex;flex-direction:column;
    cursor:pointer;transition:transform var(--t),box-shadow var(--t),border-color var(--t);
    animation:cardIn .32s ease both;
}
.product-card:hover { transform:translateY(-4px);box-shadow:var(--sh-lg);border-color:#c7d2fe; }
@keyframes cardIn { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }

.card-img { position:relative;height:195px;overflow:hidden;background:var(--slate-100);flex-shrink:0; }
.card-img img { width:100%;height:100%;object-fit:contain;padding:8px;transition:transform .4s ease;display:block; }
.product-card:hover .card-img img { transform:scale(1.05); }
.card-img-placeholder {
    width:100%;height:100%;display:flex;align-items:center;justify-content:center;
    font-size:2.75rem;color:var(--slate-300);
    background:linear-gradient(135deg,var(--slate-100) 0%,#e0e7ff 100%);
}

.badge-wrap { position:absolute;top:.6rem;right:.6rem;display:flex;flex-direction:column;gap:.3rem;z-index:3; }
.badge {
    display:inline-flex;align-items:center;gap:.28rem;padding:.28rem .65rem;
    border-radius:50px;font-size:.72rem;font-weight:800;box-shadow:0 2px 8px rgba(0,0,0,.15);white-space:nowrap;
}
.badge-discount { background:var(--amber);color:white; }
.badge-oos      { background:rgba(0,0,0,.6);color:white;backdrop-filter:blur(4px); }

.oos-overlay {
    position:absolute;inset:0;background:rgba(15,23,42,.48);
    display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.45rem;
    z-index:4;backdrop-filter:blur(2px);
}
.oos-overlay i     { font-size:1.85rem;color:#f87171; }
.oos-overlay span  { background:var(--red);color:white;font-weight:800;font-size:.82rem;padding:.3rem .9rem;border-radius:50px; }

.card-body { padding:.95rem 1.05rem 1.1rem;display:flex;flex-direction:column;gap:.45rem;flex:1; }
.card-category { font-size:.7rem;font-weight:700;color:var(--brand);text-transform:uppercase;letter-spacing:.8px; }
.card-name {
    font-size:.97rem;font-weight:700;color:var(--slate-900);line-height:1.35;margin:0;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.card-price-row { margin-top:auto;padding-top:.7rem;display:flex;align-items:flex-end;gap:.45rem; }
.card-price    { font-size:1.2rem;font-weight:900;color:var(--green);line-height:1; }
.card-original { font-size:.8rem;color:var(--slate-400);text-decoration:line-through;line-height:1; }
.card-saving   { margin-right:auto;font-size:.72rem;font-weight:700;color:var(--amber);background:var(--amber-light);padding:.18rem .5rem;border-radius:50px; }

/* ===== EMPTY STATE ===== */
.empty-state { grid-column:1/-1;text-align:center;padding:4rem 2rem;color:var(--slate-400); }
.empty-state i  { font-size:3.5rem;display:block;margin-bottom:1rem;opacity:.4; }
.empty-state h3 { font-size:1.2rem;color:var(--slate-500);margin:0 0 .4rem; }
.empty-state p  { font-size:.9rem;margin:0; }

/* ===== PAGINATION ===== */
.pagination-wrap { display:flex;justify-content:center;padding-top:.5rem; }

/* ===== MODAL ===== */
#modal { position:fixed;inset:0;z-index:9000;display:none;align-items:center;justify-content:center;padding:1rem; }
#modal.open { display:flex; }
#modal-backdrop { position:absolute;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(6px);animation:fadeIn .22s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
#modal-box {
    position:relative;z-index:1;background:white;border-radius:var(--r-xl);
    width:100%;max-width:780px;max-height:90vh;overflow:hidden;
    display:grid;grid-template-columns:1fr 1fr;box-shadow:var(--sh-xl);
    animation:slideUp .32s cubic-bezier(.175,.885,.32,1.275);
}
@keyframes slideUp { from{opacity:0;transform:translateY(28px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }

#modal-img-col { position:relative;background:var(--slate-100);min-height:360px;overflow:hidden; }
#modal-img-col img { width:100%;height:100%;object-fit:contain;padding:16px;display:block; }
#modal-img-placeholder {
    display:none;width:100%;height:100%;min-height:360px;align-items:center;justify-content:center;
    font-size:5rem;color:var(--slate-300);background:linear-gradient(135deg,var(--slate-100) 0%,#e0e7ff 100%);
}
#modal-oos-overlay {
    position:absolute;inset:0;background:rgba(15,23,42,.52);
    display:none;flex-direction:column;align-items:center;justify-content:center;gap:.7rem;
}
#modal-oos-overlay i    { font-size:2.5rem;color:#f87171; }
#modal-oos-overlay span { background:var(--red);color:white;font-weight:800;font-size:.95rem;padding:.45rem 1.4rem;border-radius:50px; }

#modal-info-col { padding:1.85rem;display:flex;flex-direction:column;gap:.95rem;overflow-y:auto; }
#modal-info-col::-webkit-scrollbar { width:5px; }
#modal-info-col::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:50px; }
#modal-category     { font-size:.73rem;font-weight:800;color:var(--brand);text-transform:uppercase;letter-spacing:1px; }
#modal-name         { font-size:1.45rem;font-weight:900;color:var(--slate-900);margin:0;line-height:1.25; }
#modal-desc         { font-size:.9rem;color:var(--slate-500);line-height:1.85;white-space:pre-wrap;flex:1; }
#modal-price-box    { background:var(--green-light);border:1.5px solid #6ee7b7;border-radius:var(--r-md);padding:1.05rem; }
#modal-price-box .label { font-size:.75rem;font-weight:700;color:var(--slate-400);margin-bottom:.35rem; }
#modal-final-price  { font-size:2rem;font-weight:900;color:var(--green);line-height:1; }
#modal-original-price { font-size:.93rem;color:var(--slate-400);text-decoration:line-through;margin-top:.28rem;display:none; }
#modal-saving       { font-size:.83rem;font-weight:700;color:var(--amber);margin-top:.35rem;display:none; }
#modal-discount-badge {
    display:none;background:var(--amber-light);color:var(--amber);border:1.5px solid #fcd34d;
    border-radius:50px;padding:.45rem 1.1rem;font-weight:800;font-size:.88rem;text-align:center;
}
#modal-close {
    position:absolute;top:.85rem;left:.85rem;width:34px;height:34px;border-radius:50%;border:none;
    background:rgba(255,255,255,.92);backdrop-filter:blur(4px);color:var(--slate-700);font-size:.95rem;
    cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:var(--sh-sm);z-index:10;
    transition:background var(--t),transform var(--t);
}
#modal-close:hover { background:white;transform:scale(1.1); }

/* ===== RESPONSIVE ===== */
@media (max-width:1024px) {
    .shop-layout { grid-template-columns:230px 1fr;gap:1.25rem; }
    .product-grid { grid-template-columns:repeat(auto-fill,minmax(195px,1fr)); }
}
@media (max-width:768px) {
    .shop-layout { grid-template-columns:1fr; }
    .sidebar { position:relative;top:auto; }
    .shop-header h1 { font-size:1.4rem; }
    .header-stats { display:none; }
    .product-grid { grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.9rem; }
    #modal-box { grid-template-columns:1fr;max-height:88vh;overflow-y:auto; }
    #modal-img-col { min-height:210px; }
    #modal-img-placeholder { min-height:210px !important; }
}
</style>
@endpush

@section('content')
<div class="container">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="shop-header">
        <div class="shop-header-inner">
            <div>
                <h1>🛍️ منتجاتنا</h1>
                <p>اكتشف أحدث المنتجات بأفضل الأسعار</p>
            </div>
            <div class="header-stats">
                <div class="stat-pill">
                    <i class="fas fa-box"></i>
                    {{ $totalCount }} منتج
                </div>
                <div class="stat-pill">
                    <i class="fas fa-tag"></i>
                    {{ $discountedCount }} عرض حالي
                </div>
                <div class="stat-pill">
                    <i class="fas fa-th-large"></i>
                    {{ $categories->count() }} تصنيف
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN FORM (GET) ===== --}}
    <form method="GET" action="{{ route('products.index') }}" id="filterForm" autocomplete="off">

        <div class="shop-layout">

            {{-- ===== SIDEBAR ===== --}}
            <aside class="sidebar">

                {{-- HIDDEN: category (يُضبط بالجافاسكريبت عند الضغط على زر تصنيف) --}}
                <input type="hidden" name="category" id="categoryInput" value="{{ request('category') }}">

                {{-- CATEGORIES --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <i class="fas fa-layer-group"></i> التصنيفات
                    </div>
                    <div class="filter-card-body">
                        <div class="cat-list">

                            {{-- زر "الكل" --}}
                            <button type="button"
                                class="cat-btn {{ !request('category') ? 'active' : '' }}"
                                onclick="selectCategory('')">
                                <span class="cat-icon"><i class="fas fa-th"></i></span>
                                <span class="cat-label">جميع المنتجات</span>
                                <span class="cat-count">{{ $totalCount }}</span>
                            </button>

                            @foreach($categories as $cat)
                                @php $cnt = \App\Models\Product::where('category', $cat)->count(); @endphp
                                <button type="button"
                                    class="cat-btn {{ request('category') === $cat ? 'active' : '' }}"
                                    onclick="selectCategory('{{ $cat }}')">
                                    <span class="cat-icon"><i class="fas fa-folder"></i></span>
                                    <span class="cat-label">{{ $cat }}</span>
                                    <span class="cat-count">{{ $cnt }}</span>
                                </button>
                            @endforeach

                        </div>
                    </div>
                </div>

                {{-- PRICE RANGE --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <i class="fas fa-shekel-sign"></i> نطاق السعر
                    </div>
                    <div class="filter-card-body">
                        <div class="price-range-wrap">
                            <div class="price-inputs">
                                <div class="price-input-group">
                                    <label>من (₪)</label>
                                    <input type="number" name="price_min" id="priceMin"
                                           min="{{ $minPrice }}" max="{{ $maxPrice }}"
                                           value="{{ request('price_min', $minPrice) }}"
                                           oninput="syncSlider()">
                                </div>
                                <div class="price-input-group">
                                    <label>إلى (₪)</label>
                                    <input type="number" name="price_max" id="priceMax"
                                           min="{{ $minPrice }}" max="{{ $maxPrice }}"
                                           value="{{ request('price_max', $maxPrice) }}"
                                           oninput="syncSlider()">
                                </div>
                            </div>
                            <div class="price-slider-track">
                                <div class="price-slider-fill" id="sliderFill"></div>
                                <input class="price-slider" type="range" id="sliderMin"
                                       min="{{ $minPrice }}" max="{{ $maxPrice }}"
                                       value="{{ request('price_min', $minPrice) }}"
                                       oninput="onSliderMin()">
                                <input class="price-slider" type="range" id="sliderMax"
                                       min="{{ $minPrice }}" max="{{ $maxPrice }}"
                                       value="{{ request('price_max', $maxPrice) }}"
                                       oninput="onSliderMax()">
                            </div>
                            <button type="submit" class="apply-price-btn">
                                <i class="fas fa-check me-1"></i> تطبيق النطاق
                            </button>
                        </div>
                    </div>
                </div>

                {{-- EXTRA FILTERS --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <i class="fas fa-sliders-h"></i> خيارات إضافية
                    </div>
                    <div class="filter-card-body">
                        <div class="toggle-row">
                            <span class="toggle-label">🏷️ عروض وخصومات فقط</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="discount" value="1" id="discountOnly"
                                       {{ request('discount') ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">✅ متوفرة فقط</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="in_stock" value="1" id="inStockOnly"
                                       {{ request('in_stock') ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- CLEAR ALL --}}
                <a href="{{ route('products.index') }}" class="clear-btn">
                    <i class="fas fa-redo"></i> إعادة تعيين الفلاتر
                </a>

            </aside>

            {{-- ===== MAIN CONTENT ===== --}}
            <main class="main-col">

                {{-- TOOLBAR --}}
                <div class="toolbar">
                    <div class="search-wrap">
                        <input type="text" name="search" id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="ابحث باسم المنتج...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <select name="sort" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="latest"     {{ request('sort','latest') === 'latest'     ? 'selected' : '' }}>الأحدث أولاً</option>
                        <option value="price-asc"  {{ request('sort') === 'price-asc'           ? 'selected' : '' }}>السعر: تصاعدي</option>
                        <option value="price-desc" {{ request('sort') === 'price-desc'          ? 'selected' : '' }}>السعر: تنازلي</option>
                        <option value="discount"   {{ request('sort') === 'discount'            ? 'selected' : '' }}>الأكثر خصماً</option>
                        <option value="alpha"      {{ request('sort') === 'alpha'               ? 'selected' : '' }}>الاسم أ–ي</option>
                    </select>
                    <div class="results-count">
                        <strong>{{ $products->total() }}</strong> نتيجة
                    </div>
                </div>

                {{-- ===== ACTIVE FILTER CHIPS ===== --}}
                @php
                    $hasFilters = request()->hasAny(['search','category','price_min','price_max','discount','in_stock']);
                @endphp
                @if($hasFilters)
                <div class="active-filters">

                    @if(request('search'))
                        @php $p = request()->except(['search','page']); @endphp
                        <a href="{{ route('products.index', $p) }}" class="filter-chip">
                            🔍 {{ request('search') }} <span>✕</span>
                        </a>
                    @endif

                    @if(request('category'))
                        @php $p = request()->except(['category','page']); @endphp
                        <a href="{{ route('products.index', $p) }}" class="filter-chip">
                            📂 {{ request('category') }} <span>✕</span>
                        </a>
                    @endif

                    @if(request('price_min') != $minPrice || request('price_max') != $maxPrice)
                        @if(request('price_min') || request('price_max'))
                            @php $p = request()->except(['price_min','price_max','page']); @endphp
                            <a href="{{ route('products.index', $p) }}" class="filter-chip">
                                💰 ₪{{ request('price_min', $minPrice) }}–₪{{ request('price_max', $maxPrice) }} <span>✕</span>
                            </a>
                        @endif
                    @endif

                    @if(request('discount'))
                        @php $p = request()->except(['discount','page']); @endphp
                        <a href="{{ route('products.index', $p) }}" class="filter-chip">
                            🏷️ عروض فقط <span>✕</span>
                        </a>
                    @endif

                    @if(request('in_stock'))
                        @php $p = request()->except(['in_stock','page']); @endphp
                        <a href="{{ route('products.index', $p) }}" class="filter-chip">
                            ✅ متوفرة فقط <span>✕</span>
                        </a>
                    @endif

                </div>
                @endif

                {{-- ===== PRODUCTS GRID ===== --}}
                @if($products->count() > 0)
                <div class="product-grid">
                    @foreach ($products as $index => $product)
                        @php
                            $finalPrice = $product->discount > 0
                                ? $product->price * (1 - $product->discount / 100)
                                : $product->price;
                            $saving = $product->price - $finalPrice;
                        @endphp
                        <div class="product-card"
                             style="animation-delay: {{ $index * 40 }}ms"
                             onclick="openModal(
                                {{ json_encode($product->name) }},
                                {{ json_encode($product->category) }},
                                {{ $product->price }},
                                {{ $product->discount }},
                                {{ json_encode($product->image ? asset('storage/'.$product->image) : '') }},
                                {{ json_encode($product->description ?? '') }},
                                {{ $product->is_out_of_stock ? 'true' : 'false' }}
                             )">

                            {{-- IMAGE --}}
                            <div class="card-img">
                                @if($product->image && file_exists(storage_path('app/public/'.$product->image)))
                                    <img src="{{ asset('storage/'.$product->image) }}"
                                         alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="card-img-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                @endif

                                {{-- BADGES --}}
                                <div class="badge-wrap">
                                    @if($product->discount > 0)
                                        <span class="badge badge-discount">
                                            <i class="fas fa-tag"></i> {{ $product->discount }}% خصم
                                        </span>
                                    @endif
                                    @if($product->is_out_of_stock)
                                        <span class="badge badge-oos">
                                            <i class="fas fa-ban"></i> نفذت الكمية
                                        </span>
                                    @endif
                                </div>

                                {{-- OOS OVERLAY --}}
                                @if($product->is_out_of_stock)
                                    <div class="oos-overlay">
                                        <i class="fas fa-ban"></i>
                                        <span>نفذت الكمية</span>
                                    </div>
                                @endif
                            </div>

                            {{-- BODY --}}
                            <div class="card-body">
                                <div class="card-category">{{ $product->category }}</div>
                                <h3 class="card-name">{{ $product->name }}</h3>
                                <div class="card-price-row">
                                    <div>
                                        <div class="card-price">₪{{ number_format($finalPrice, 2) }}</div>
                                        @if($product->discount > 0)
                                            <div class="card-original">₪{{ number_format($product->price, 2) }}</div>
                                        @endif
                                    </div>
                                    @if($product->discount > 0)
                                        <span class="card-saving">وفّر ₪{{ number_format($saving, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- PAGINATION --}}
                @if($products->hasPages())
                <div class="pagination-wrap">
                    {{ $products->links() }}
                </div>
                @endif

                @else
                <div class="product-grid">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>لا توجد نتائج مطابقة</h3>
                        <p>جرّب تعديل الفلاتر أو ابحث بكلمة مختلفة</p>
                    </div>
                </div>
                @endif

            </main>
        </div>
    </form>

</div>

{{-- ===== MODAL ===== --}}
<div id="modal">
    <div id="modal-backdrop" onclick="closeModal()"></div>
    <div id="modal-box">
        <button id="modal-close" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>

        <div id="modal-img-col">
            <img id="modal-img" src="" alt="">
            <div id="modal-img-placeholder">
                <i class="fas fa-box"></i>
            </div>
            <div id="modal-oos-overlay">
                <i class="fas fa-ban"></i>
                <span>نفذت الكمية</span>
            </div>
        </div>

        <div id="modal-info-col">
            <div id="modal-category"></div>
            <h2 id="modal-name"></h2>
            <p id="modal-desc"></p>
            <div id="modal-price-box">
                <div class="label">السعر الحالي</div>
                <div id="modal-final-price"></div>
                <div id="modal-original-price"></div>
                <div id="modal-saving"></div>
            </div>
            <div id="modal-discount-badge"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* =====================================================
   CONSTANTS
===================================================== */
const PRICE_ABS_MIN = {{ $minPrice }};
const PRICE_ABS_MAX = {{ $maxPrice }};

/* =====================================================
   CATEGORY SELECTION
===================================================== */
function selectCategory(cat) {
    document.getElementById('categoryInput').value = cat;
    // إزالة صفحة الباجينيشن عند تغيير التصنيف
    const pageInputs = document.querySelectorAll('input[name="page"]');
    pageInputs.forEach(i => i.remove());
    document.getElementById('filterForm').submit();
}

/* =====================================================
   SEARCH (debounce 450ms)
===================================================== */
let searchTimer;
document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 450);
});

/* =====================================================
   PRICE SLIDER
===================================================== */
function onSliderMin() {
    const sMin = document.getElementById('sliderMin');
    const sMax = document.getElementById('sliderMax');
    if (parseInt(sMin.value) > parseInt(sMax.value)) sMin.value = sMax.value;
    document.getElementById('priceMin').value = sMin.value;
    updateSliderFill();
}
function onSliderMax() {
    const sMin = document.getElementById('sliderMin');
    const sMax = document.getElementById('sliderMax');
    if (parseInt(sMax.value) < parseInt(sMin.value)) sMax.value = sMin.value;
    document.getElementById('priceMax').value = sMax.value;
    updateSliderFill();
}
function syncSlider() {
    const min = parseInt(document.getElementById('priceMin').value) || PRICE_ABS_MIN;
    const max = parseInt(document.getElementById('priceMax').value) || PRICE_ABS_MAX;
    document.getElementById('sliderMin').value = Math.min(min, max);
    document.getElementById('sliderMax').value = Math.max(min, max);
    updateSliderFill();
}
function updateSliderFill() {
    const min  = parseInt(document.getElementById('sliderMin').value);
    const max  = parseInt(document.getElementById('sliderMax').value);
    const pct  = v => ((v - PRICE_ABS_MIN) / (PRICE_ABS_MAX - PRICE_ABS_MIN)) * 100;
    const fill = document.getElementById('sliderFill');
    fill.style.left  = pct(min) + '%';
    fill.style.width = (pct(max) - pct(min)) + '%';
}
// تهيئة الـ fill عند تحميل الصفحة
updateSliderFill();

/* =====================================================
   MODAL
===================================================== */
function openModal(name, category, price, discount, image, description, isOOS) {
    document.getElementById('modal-name').textContent     = name;
    document.getElementById('modal-category').textContent = category;
    document.getElementById('modal-desc').textContent     = description || 'لا يوجد وصف لهذا المنتج.';

    const img = document.getElementById('modal-img');
    const ph  = document.getElementById('modal-img-placeholder');
    if (image) {
        img.src = image; img.alt = name;
        img.style.display = 'block'; ph.style.display = 'none';
    } else {
        img.style.display = 'none'; ph.style.display = 'flex';
    }

    document.getElementById('modal-oos-overlay').style.display = isOOS ? 'flex' : 'none';

    const final  = discount > 0 ? price * (1 - discount / 100) : price;
    const saving = price - final;
    document.getElementById('modal-final-price').textContent = '₪' + final.toFixed(2);

    const origEl  = document.getElementById('modal-original-price');
    const savEl   = document.getElementById('modal-saving');
    const badgeEl = document.getElementById('modal-discount-badge');

    if (discount > 0) {
        origEl.textContent  = '₪' + price.toFixed(2);  origEl.style.display  = 'block';
        savEl.textContent   = 'توفير ₪' + saving.toFixed(2); savEl.style.display = 'block';
        badgeEl.textContent = '🏷️ خصم ' + discount + '%'; badgeEl.style.display = 'block';
    } else {
        origEl.style.display = savEl.style.display = badgeEl.style.display = 'none';
    }

    document.getElementById('modal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('modal').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
