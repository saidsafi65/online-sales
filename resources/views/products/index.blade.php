@extends('layout.gust')

@section('title', 'منتجاتنا')

@push('styles')
<style>
    /* ===== RESET & BASE ===== */
    *, *::before, *::after { box-sizing: border-box; }

    :root {
        --brand:       #4f46e5;
        --brand-light: #e0e7ff;
        --brand-dark:  #3730a3;
        --green:       #059669;
        --green-light: #d1fae5;
        --amber:       #d97706;
        --amber-light: #fef3c7;
        --red:         #dc2626;
        --slate-50:    #f8fafc;
        --slate-100:   #f1f5f9;
        --slate-200:   #e2e8f0;
        --slate-300:   #cbd5e1;
        --slate-400:   #94a3b8;
        --slate-500:   #64748b;
        --slate-700:   #334155;
        --slate-900:   #0f172a;
        --radius-sm:   8px;
        --radius-md:   14px;
        --radius-lg:   20px;
        --radius-xl:   28px;
        --shadow-sm:   0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md:   0 4px 16px rgba(0,0,0,.08);
        --shadow-lg:   0 12px 36px rgba(0,0,0,.12);
        --shadow-xl:   0 24px 60px rgba(0,0,0,.18);
        --transition:  0.25s cubic-bezier(.4,0,.2,1);
    }

    /* ===== PAGE LAYOUT ===== */
    .shop-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.75rem;
        align-items: start;
        padding: 1.5rem 0 3rem;
    }

    /* ===== PAGE HEADER ===== */
    .shop-header {
        margin-bottom: 1.75rem;
        padding: 2rem 2.25rem;
        border-radius: var(--radius-xl);
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 45%, #4338ca 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }
    .shop-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }
    .shop-header-inner {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .shop-header h1 {
        font-size: 1.9rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
    }
    .shop-header p {
        font-size: 1rem;
        opacity: .75;
        margin: .35rem 0 0;
    }
    .header-stats {
        display: flex;
        gap: 1rem;
    }
    .stat-pill {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 50px;
        padding: .45rem 1.1rem;
        font-size: .85rem;
        font-weight: 600;
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
        position: sticky;
        top: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .filter-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .filter-card-header {
        padding: .9rem 1.25rem;
        background: var(--slate-50);
        border-bottom: 1px solid var(--slate-200);
        font-size: .8rem;
        font-weight: 700;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: .8px;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .filter-card-body { padding: 1rem 1.1rem; }

    /* ===== CATEGORY ITEMS ===== */
    .cat-list { display: flex; flex-direction: column; gap: .25rem; }
    .cat-btn {
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .7rem .9rem;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background var(--transition), color var(--transition);
        border: none;
        background: transparent;
        width: 100%;
        text-align: right;
        color: var(--slate-700);
        font-size: .95rem;
        font-weight: 500;
        font-family: inherit;
    }
    .cat-btn:hover { background: var(--slate-50); color: var(--brand); }
    .cat-btn.active {
        background: var(--brand-light);
        color: var(--brand-dark);
        font-weight: 700;
    }
    .cat-btn .cat-icon {
        width: 30px; height: 30px;
        border-radius: var(--radius-sm);
        background: var(--slate-100);
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
        transition: background var(--transition);
    }
    .cat-btn.active .cat-icon { background: var(--brand); color: white; }
    .cat-btn .cat-label { flex: 1; }
    .cat-count {
        background: var(--slate-100);
        color: var(--slate-500);
        font-size: .75rem;
        font-weight: 700;
        padding: .2rem .6rem;
        border-radius: 50px;
        transition: all var(--transition);
    }
    .cat-btn.active .cat-count { background: var(--brand); color: white; }

    /* ===== PRICE RANGE ===== */
    .price-range-wrap { display: flex; flex-direction: column; gap: .85rem; }
    .price-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; }
    .price-input-group label {
        display: block; font-size: .75rem; font-weight: 600;
        color: var(--slate-400); margin-bottom: .3rem;
    }
    .price-input-group input {
        width: 100%;
        padding: .55rem .75rem;
        border: 1.5px solid var(--slate-200);
        border-radius: var(--radius-sm);
        font-size: .9rem;
        font-family: inherit;
        transition: border-color var(--transition);
        color: var(--slate-700);
        font-weight: 600;
    }
    .price-input-group input:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    }
    .price-slider-track {
        position: relative; height: 4px;
        background: var(--slate-200);
        border-radius: 50px;
        margin: .5rem 0;
    }
    .price-slider-fill {
        position: absolute; height: 100%;
        background: var(--brand);
        border-radius: 50px;
    }
    .price-slider {
        position: absolute;
        top: 50%; transform: translateY(-50%);
        width: 100%; height: 4px;
        -webkit-appearance: none;
        appearance: none;
        background: transparent;
        cursor: pointer;
    }
    .price-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px; height: 18px;
        border-radius: 50%;
        background: var(--brand);
        border: 2.5px solid white;
        box-shadow: 0 2px 8px rgba(79,70,229,.4);
        cursor: pointer;
        position: relative; z-index: 2;
    }
    .apply-price-btn {
        width: 100%;
        padding: .65rem;
        border: none;
        border-radius: var(--radius-sm);
        background: var(--brand);
        color: white;
        font-family: inherit;
        font-weight: 700;
        font-size: .9rem;
        cursor: pointer;
        transition: background var(--transition), transform var(--transition);
    }
    .apply-price-btn:hover { background: var(--brand-dark); transform: translateY(-1px); }

    /* ===== DISCOUNT FILTER ===== */
    .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .65rem 0;
    }
    .toggle-label { font-size: .9rem; font-weight: 600; color: var(--slate-700); }
    .toggle-switch {
        position: relative;
        width: 42px; height: 24px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0;
        background: var(--slate-200);
        border-radius: 50px;
        cursor: pointer;
        transition: background var(--transition);
    }
    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 18px; height: 18px;
        left: 3px; top: 3px;
        background: white;
        border-radius: 50%;
        transition: transform var(--transition);
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    .toggle-switch input:checked + .toggle-slider { background: var(--brand); }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(18px); }

    /* ===== CLEAR FILTERS ===== */
    .clear-btn {
        width: 100%;
        padding: .65rem;
        border: 1.5px solid var(--slate-200);
        border-radius: var(--radius-sm);
        background: white;
        color: var(--slate-500);
        font-family: inherit;
        font-weight: 600;
        font-size: .88rem;
        cursor: pointer;
        transition: all var(--transition);
        display: flex; align-items: center; justify-content: center; gap: .5rem;
    }
    .clear-btn:hover { border-color: var(--red); color: var(--red); background: #fff5f5; }

    /* ===== MAIN CONTENT ===== */
    .main-col { display: flex; flex-direction: column; gap: 1.25rem; }

    /* ===== TOOLBAR ===== */
    .toolbar {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        padding: 1rem 1.25rem;
        display: flex;
        gap: 1rem;
        align-items: center;
        border: 1px solid var(--slate-200);
    }
    .search-wrap {
        flex: 1;
        position: relative;
    }
    .search-wrap input {
        width: 100%;
        padding: .75rem 1rem .75rem 2.75rem;
        border: 1.5px solid var(--slate-200);
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: .95rem;
        color: var(--slate-700);
        transition: border-color var(--transition), box-shadow var(--transition);
        background: var(--slate-50);
    }
    .search-wrap input::placeholder { color: var(--slate-400); }
    .search-wrap input:focus {
        outline: none;
        border-color: var(--brand);
        background: white;
        box-shadow: 0 0 0 3px rgba(79,70,229,.1);
    }
    .search-icon {
        position: absolute;
        left: .9rem; top: 50%;
        transform: translateY(-50%);
        color: var(--slate-400);
        font-size: .95rem;
        pointer-events: none;
        transition: color var(--transition);
    }
    .search-wrap input:focus ~ .search-icon { color: var(--brand); }
    .sort-select {
        padding: .75rem 1rem;
        border: 1.5px solid var(--slate-200);
        border-radius: var(--radius-md);
        background: var(--slate-50);
        font-family: inherit;
        font-size: .9rem;
        color: var(--slate-700);
        font-weight: 600;
        cursor: pointer;
        transition: border-color var(--transition);
        flex-shrink: 0;
    }
    .sort-select:focus { outline: none; border-color: var(--brand); }
    .results-count {
        font-size: .85rem;
        color: var(--slate-400);
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .results-count span { color: var(--brand); font-weight: 800; }

    /* ===== ACTIVE FILTERS ===== */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }
    .active-filters:empty { display: none; }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: var(--brand-light);
        color: var(--brand-dark);
        border-radius: 50px;
        padding: .3rem .85rem;
        font-size: .82rem;
        font-weight: 700;
        cursor: pointer;
        border: 1.5px solid #c7d2fe;
        transition: all var(--transition);
    }
    .filter-chip:hover { background: #c7d2fe; }
    .filter-chip .chip-x { font-size: .75rem; opacity: .7; }
    .filters-label {
        font-size: .8rem;
        font-weight: 600;
        color: var(--slate-400);
    }

    /* ===== PRODUCT GRID ===== */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 1.25rem;
    }

    /* ===== PRODUCT CARD ===== */
    .product-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--slate-200);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: #c7d2fe;
    }

    /* image */
    .card-img {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: var(--slate-100);
        flex-shrink: 0;
    }
    .card-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .45s ease;
        display: block;
    }
    .product-card:hover .card-img img { transform: scale(1.06); }
    .card-img-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem;
        color: var(--slate-300);
        background: linear-gradient(135deg, var(--slate-100) 0%, #e0e7ff 100%);
    }

    /* badges */
    .badge-wrap {
        position: absolute;
        top: .65rem;
        right: .65rem;
        display: flex;
        flex-direction: column;
        gap: .35rem;
        z-index: 3;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .7rem;
        border-radius: 50px;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .3px;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
        white-space: nowrap;
    }
    .badge-new { background: var(--brand); color: white; }
    .badge-discount { background: var(--amber); color: white; }
    .badge-oos {
        background: rgba(0,0,0,.6);
        color: white;
        backdrop-filter: blur(4px);
    }

    /* out of stock overlay */
    .oos-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15,23,42,.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        z-index: 4;
        backdrop-filter: blur(2px);
    }
    .oos-overlay i { font-size: 2rem; color: #f87171; }
    .oos-overlay span {
        background: var(--red);
        color: white;
        font-weight: 800;
        font-size: .85rem;
        padding: .35rem 1rem;
        border-radius: 50px;
    }

    /* card body */
    .card-body {
        padding: 1rem 1.1rem 1.2rem;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        flex: 1;
    }
    .card-category {
        font-size: .72rem;
        font-weight: 700;
        color: var(--brand);
        text-transform: uppercase;
        letter-spacing: .8px;
    }
    .card-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--slate-900);
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
    }
    .card-price-row {
        margin-top: auto;
        padding-top: .75rem;
        display: flex;
        align-items: flex-end;
        gap: .5rem;
    }
    .card-price {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--green);
        line-height: 1;
    }
    .card-original {
        font-size: .82rem;
        color: var(--slate-400);
        text-decoration: line-through;
        line-height: 1;
    }
    .card-saving {
        margin-right: auto;
        font-size: .75rem;
        font-weight: 700;
        color: var(--amber);
        background: var(--amber-light);
        padding: .2rem .55rem;
        border-radius: 50px;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        color: var(--slate-400);
    }
    .empty-state i { font-size: 3.5rem; display: block; margin-bottom: 1rem; opacity: .45; }
    .empty-state h3 { font-size: 1.2rem; color: var(--slate-500); margin: 0 0 .4rem; }
    .empty-state p { font-size: .9rem; margin: 0; }

    /* ===== PAGINATION ===== */
    .pagination-wrap {
        display: flex;
        justify-content: center;
        padding-top: .5rem;
    }

    /* ===== MODAL ===== */
    #modal {
        position: fixed;
        inset: 0;
        z-index: 9000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    #modal.open { display: flex; }
    #modal-backdrop {
        position: absolute; inset: 0;
        background: rgba(15,23,42,.6);
        backdrop-filter: blur(6px);
        animation: fadeIn .25s ease;
    }
    #modal-box {
        position: relative;
        z-index: 1;
        background: white;
        border-radius: var(--radius-xl);
        width: 100%;
        max-width: 780px;
        max-height: 90vh;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        box-shadow: var(--shadow-xl);
        animation: slideUp .35s cubic-bezier(.175,.885,.32,1.275);
    }
    #modal-img-col {
        position: relative;
        background: var(--slate-100);
        min-height: 380px;
        overflow: hidden;
    }
    #modal-img-col img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }
    #modal-img-placeholder {
        display: none;
        width: 100%; height: 100%;
        min-height: 380px;
        align-items: center; justify-content: center;
        font-size: 5rem;
        color: var(--slate-300);
        background: linear-gradient(135deg, var(--slate-100) 0%, #e0e7ff 100%);
    }
    #modal-oos-overlay {
        position: absolute; inset: 0;
        background: rgba(15,23,42,.55);
        display: none;
        flex-direction: column;
        align-items: center; justify-content: center;
        gap: .75rem;
    }
    #modal-oos-overlay i { font-size: 2.75rem; color: #f87171; }
    #modal-oos-overlay span {
        background: var(--red); color: white;
        font-weight: 800; font-size: 1rem;
        padding: .5rem 1.5rem; border-radius: 50px;
    }
    #modal-info-col {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        overflow-y: auto;
    }
    #modal-info-col::-webkit-scrollbar { width: 5px; }
    #modal-info-col::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 50px; }

    #modal-category {
        font-size: .75rem; font-weight: 800;
        color: var(--brand); text-transform: uppercase;
        letter-spacing: 1px;
    }
    #modal-name {
        font-size: 1.5rem; font-weight: 900;
        color: var(--slate-900); margin: 0;
        line-height: 1.25;
    }
    #modal-desc {
        font-size: .92rem;
        color: var(--slate-500);
        line-height: 1.8;
        white-space: pre-wrap;
        flex: 1;
    }
    #modal-price-box {
        background: var(--green-light);
        border: 1.5px solid #6ee7b7;
        border-radius: var(--radius-md);
        padding: 1.1rem;
    }
    #modal-price-box .label {
        font-size: .78rem; font-weight: 700;
        color: var(--slate-400); margin-bottom: .4rem;
    }
    #modal-final-price {
        font-size: 2.1rem; font-weight: 900;
        color: var(--green); line-height: 1;
    }
    #modal-original-price {
        font-size: .95rem; color: var(--slate-400);
        text-decoration: line-through; margin-top: .3rem;
        display: none;
    }
    #modal-saving {
        font-size: .85rem; font-weight: 700;
        color: var(--amber); margin-top: .4rem;
        display: none;
    }
    #modal-discount-badge {
        display: none;
        background: var(--amber-light);
        color: var(--amber);
        border: 1.5px solid #fcd34d;
        border-radius: 50px;
        padding: .5rem 1.2rem;
        font-weight: 800;
        font-size: .9rem;
        text-align: center;
    }
    #modal-close {
        position: absolute; top: .9rem; left: .9rem;
        width: 36px; height: 36px;
        border-radius: 50%; border: none;
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(4px);
        color: var(--slate-700);
        font-size: 1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: var(--shadow-sm);
        z-index: 10;
        transition: background var(--transition), transform var(--transition);
    }
    #modal-close:hover { background: white; transform: scale(1.1); }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .product-card {
        animation: cardIn .35s ease both;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .shop-layout { grid-template-columns: 240px 1fr; gap: 1.25rem; }
        .product-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
    }
    @media (max-width: 768px) {
        .shop-layout { grid-template-columns: 1fr; }
        .sidebar { position: relative; top: auto; }
        .shop-header h1 { font-size: 1.4rem; }
        .header-stats { display: none; }
        .toolbar { flex-wrap: wrap; gap: .75rem; }
        .results-count { order: -1; width: 100%; }
        .product-grid { grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); gap: 1rem; }
        #modal-box { grid-template-columns: 1fr; max-height: 88vh; overflow-y: auto; }
        #modal-img-col { min-height: 220px; }
        #modal-img-placeholder { min-height: 220px !important; }
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
                    {{ $products->total() }} منتج
                </div>
                @php
                    $catCount = App\Models\Product::distinct()->count('category');
                    $discountedCount = App\Models\Product::where('discount', '>', 0)->count();
                @endphp
                <div class="stat-pill">
                    <i class="fas fa-tag"></i>
                    {{ $discountedCount }} عرض حالي
                </div>
                <div class="stat-pill">
                    <i class="fas fa-th-large"></i>
                    {{ $catCount }} تصنيف
                </div>
            </div>
        </div>
    </div>

    <div class="shop-layout">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="sidebar">

            {{-- CATEGORIES --}}
            <div class="filter-card">
                <div class="filter-card-header">
                    <i class="fas fa-layer-group"></i> التصنيفات
                </div>
                <div class="filter-card-body">
                    <div class="cat-list">
                        <button class="cat-btn active" data-category="all" onclick="setCategory(this,'all')">
                            <span class="cat-icon"><i class="fas fa-th"></i></span>
                            <span class="cat-label">جميع المنتجات</span>
                            <span class="cat-count" id="cat-all-count">{{ $products->total() }}</span>
                        </button>

                        @section('content')
@php
    $categories      = App\Models\Product::distinct()->pluck('category');
    $catCount        = $categories->count();
    $discountedCount = App\Models\Product::where('discount', '>', 0)->count();
    $minPrice        = (int) floor(App\Models\Product::min('price') ?? 0);
    $maxPrice        = (int) ceil(App\Models\Product::max('price') ?? 1000);
@endphp

                        @foreach($categories as $cat)
                        @php $cnt = App\Models\Product::where('category', $cat)->count(); @endphp
                        <button class="cat-btn" data-category="{{ $cat }}" onclick="setCategory(this,'{{ $cat }}')">
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
                        @php
                            $minPrice = (int) floor(App\Models\Product::min('price') ?? 0);
                            $maxPrice = (int) ceil(App\Models\Product::max('price') ?? 1000);
                        @endphp
                        <div class="price-inputs">
                            <div class="price-input-group">
                                <label>من (₪)</label>
                                <input type="number" id="priceMin" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $minPrice }}" oninput="syncSlider()">
                            </div>
                            <div class="price-input-group">
                                <label>إلى (₪)</label>
                                <input type="number" id="priceMax" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $maxPrice }}" oninput="syncSlider()">
                            </div>
                        </div>
                        <div class="price-slider-track">
                            <div class="price-slider-fill" id="sliderFill"></div>
                            <input class="price-slider" type="range" id="sliderMin" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $minPrice }}" oninput="onSliderMin()">
                            <input class="price-slider" type="range" id="sliderMax" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ $maxPrice }}" oninput="onSliderMax()">
                        </div>
                        <button class="apply-price-btn" onclick="applyPriceFilter()">
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
                            <input type="checkbox" id="discountOnly" onchange="applyFilters()">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-row">
                        <span class="toggle-label">✅ متوفرة فقط</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="inStockOnly" onchange="applyFilters()">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- CLEAR --}}
            <button class="clear-btn" onclick="clearAllFilters()">
                <i class="fas fa-redo"></i> إعادة تعيين الفلاتر
            </button>

        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <main class="main-col">

            {{-- TOOLBAR --}}
            <div class="toolbar">
                <div class="search-wrap">
                    <input type="text" id="searchInput" placeholder="ابحث باسم المنتج...">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <select class="sort-select" id="sortSelect" onchange="sortProducts()">
                    <option value="latest">الأحدث أولاً</option>
                    <option value="price-asc">السعر: تصاعدي</option>
                    <option value="price-desc">السعر: تنازلي</option>
                    <option value="discount">الأكثر خصماً</option>
                    <option value="alpha">الاسم أ–ي</option>
                </select>
                <div class="results-count">
                    <span id="visibleCount">{{ $products->count() }}</span> نتيجة
                </div>
            </div>

            {{-- ACTIVE FILTER CHIPS --}}
            <div class="active-filters" id="activeFilters"></div>

            {{-- PRODUCTS GRID --}}
            @if($products->count() > 0)
            <div class="product-grid" id="productsGrid">
                @foreach ($products as $product)
                @php
                    $finalPrice = $product->discount > 0
                        ? $product->price * (1 - $product->discount / 100)
                        : $product->price;
                    $saving = $product->price - $finalPrice;
                @endphp
                <div class="product-card"
                     data-name="{{ mb_strtolower($product->name) }}"
                     data-category="{{ $product->category }}"
                     data-price="{{ $product->price }}"
                     data-final="{{ number_format($finalPrice, 2, '.', '') }}"
                     data-discount="{{ $product->discount }}"
                     data-oos="{{ $product->is_out_of_stock ? '1' : '0' }}"
                     data-id="{{ $product->id }}"
                     onclick="openModal(
                        '{{ addslashes($product->name) }}',
                        '{{ addslashes($product->category) }}',
                        {{ $product->price }},
                        {{ $product->discount }},
                        '{{ $product->image ? asset('storage/'.$product->image) : '' }}',
                        `{{ addslashes($product->description ?? '') }}`,
                        {{ $product->is_out_of_stock ? 'true' : 'false' }}
                     )">

                    {{-- IMAGE --}}
                    <div class="card-img">
                        @if($product->image && file_exists(storage_path('app/public/'.$product->image)))
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy">
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

                        {{-- OUT OF STOCK OVERLAY --}}
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
                    <i class="fas fa-box-open"></i>
                    <h3>لا توجد منتجات متاحة</h3>
                    <p>يرجى المحاولة لاحقاً</p>
                </div>
            </div>
            @endif

        </main>
    </div>
</div>

{{-- ===== MODAL ===== --}}
<div id="modal">
    <div id="modal-backdrop" onclick="closeModal()"></div>
    <div id="modal-box">
        <button id="modal-close" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>

        {{-- IMAGE --}}
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

        {{-- INFO --}}
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
/* ===================================================
   STATE
=================================================== */
const PRICE_ABS_MIN = {{ $minPrice ?? 0 }};
const PRICE_ABS_MAX = {{ $maxPrice ?? 10000 }};

let state = {
    category : 'all',
    search   : '',
    priceMin : PRICE_ABS_MIN,
    priceMax : PRICE_ABS_MAX,
    discount : false,
    inStock  : false,
    sort     : 'latest',
};

const originalOrder = [...document.querySelectorAll('.product-card')];

/* ===================================================
   CATEGORY
=================================================== */
function setCategory(btn, cat) {
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    state.category = cat;
    applyFilters();
}

/* ===================================================
   SEARCH
=================================================== */
document.getElementById('searchInput').addEventListener('input', function() {
    state.search = this.value.trim().toLowerCase();
    applyFilters();
});

/* ===================================================
   PRICE SLIDER
=================================================== */
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
    const min = parseInt(document.getElementById('sliderMin').value);
    const max = parseInt(document.getElementById('sliderMax').value);
    const pct  = (v) => ((v - PRICE_ABS_MIN) / (PRICE_ABS_MAX - PRICE_ABS_MIN)) * 100;
    const fill = document.getElementById('sliderFill');
    fill.style.left  = pct(min) + '%';
    fill.style.width = (pct(max) - pct(min)) + '%';
}
function applyPriceFilter() {
    state.priceMin = parseInt(document.getElementById('priceMin').value) || PRICE_ABS_MIN;
    state.priceMax = parseInt(document.getElementById('priceMax').value) || PRICE_ABS_MAX;
    applyFilters();
}
updateSliderFill();

/* ===================================================
   TOGGLES
=================================================== */
document.getElementById('discountOnly').addEventListener('change', function() {
    state.discount = this.checked; applyFilters();
});
document.getElementById('inStockOnly').addEventListener('change', function() {
    state.inStock = this.checked; applyFilters();
});

/* ===================================================
   SORT
=================================================== */
function sortProducts() {
    state.sort = document.getElementById('sortSelect').value;
    applyFilters();
}

/* ===================================================
   CLEAR
=================================================== */
function clearAllFilters() {
    state = { category:'all', search:'', priceMin:PRICE_ABS_MIN, priceMax:PRICE_ABS_MAX,
               discount:false, inStock:false, sort:'latest' };
    document.getElementById('searchInput').value = '';
    document.getElementById('sortSelect').value = 'latest';
    document.getElementById('discountOnly').checked = false;
    document.getElementById('inStockOnly').checked = false;
    document.getElementById('priceMin').value = PRICE_ABS_MIN;
    document.getElementById('priceMax').value = PRICE_ABS_MAX;
    document.getElementById('sliderMin').value = PRICE_ABS_MIN;
    document.getElementById('sliderMax').value = PRICE_ABS_MAX;
    updateSliderFill();
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('[data-category="all"]').classList.add('active');
    applyFilters();
}

/* ===================================================
   CORE FILTER + SORT
=================================================== */
function applyFilters() {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    /* filter */
    let visible = [];
    let all = [...document.querySelectorAll('.product-card')];

    all.forEach(card => {
        const name     = card.dataset.name;
        const cat      = card.dataset.category;
        const price    = parseFloat(card.dataset.final);
        const discount = parseInt(card.dataset.discount);
        const oos      = card.dataset.oos === '1';

        const matchCat      = state.category === 'all' || cat === state.category;
        const matchSearch   = !state.search || name.includes(state.search);
        const matchPrice    = price >= state.priceMin && price <= state.priceMax;
        const matchDiscount = !state.discount || discount > 0;
        const matchStock    = !state.inStock || !oos;

        const show = matchCat && matchSearch && matchPrice && matchDiscount && matchStock;
        card.style.display = show ? '' : 'none';
        if (show) visible.push(card);
    });

    /* sort */
    const sorted = [...visible].sort((a, b) => {
        switch (state.sort) {
            case 'price-asc':  return parseFloat(a.dataset.final) - parseFloat(b.dataset.final);
            case 'price-desc': return parseFloat(b.dataset.final) - parseFloat(a.dataset.final);
            case 'discount':   return parseInt(b.dataset.discount) - parseInt(a.dataset.discount);
            case 'alpha':      return a.dataset.name.localeCompare(b.dataset.name, 'ar');
            default:           return originalOrder.indexOf(a) - originalOrder.indexOf(b);
        }
    });

    /* reorder DOM */
    sorted.forEach(c => grid.appendChild(c));

    /* count */
    document.getElementById('visibleCount').textContent = visible.length;

    /* empty state */
    let empty = grid.querySelector('.empty-message');
    if (visible.length === 0) {
        if (!empty) {
            empty = document.createElement('div');
            empty.className = 'empty-message empty-state';
            empty.style.cssText = 'grid-column:1/-1';
            empty.innerHTML = `<i class="fas fa-search"></i>
                <h3>لا توجد نتائج</h3>
                <p>جرّب تعديل الفلاتر أو البحث بكلمة مختلفة</p>`;
            grid.appendChild(empty);
        }
    } else {
        if (empty) empty.remove();
    }

    /* chips */
    renderChips();
}

/* ===================================================
   ACTIVE FILTER CHIPS
=================================================== */
function renderChips() {
    const wrap = document.getElementById('activeFilters');
    wrap.innerHTML = '';

    const add = (label, clear) => {
        const c = document.createElement('span');
        c.className = 'filter-chip';
        c.innerHTML = label + ' <span class="chip-x">✕</span>';
        c.onclick = clear;
        wrap.appendChild(c);
    };

    if (state.category !== 'all')
        add('📂 ' + state.category, () => {
            state.category = 'all';
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('[data-category="all"]').classList.add('active');
            applyFilters();
        });

    if (state.search)
        add('🔍 ' + state.search, () => {
            state.search = '';
            document.getElementById('searchInput').value = '';
            applyFilters();
        });

    if (state.priceMin > PRICE_ABS_MIN || state.priceMax < PRICE_ABS_MAX)
        add(`💰 ₪${state.priceMin}–₪${state.priceMax}`, () => {
            state.priceMin = PRICE_ABS_MIN; state.priceMax = PRICE_ABS_MAX;
            document.getElementById('priceMin').value = PRICE_ABS_MIN;
            document.getElementById('priceMax').value = PRICE_ABS_MAX;
            document.getElementById('sliderMin').value = PRICE_ABS_MIN;
            document.getElementById('sliderMax').value = PRICE_ABS_MAX;
            updateSliderFill(); applyFilters();
        });

    if (state.discount)
        add('🏷️ عروض فقط', () => {
            state.discount = false;
            document.getElementById('discountOnly').checked = false;
            applyFilters();
        });

    if (state.inStock)
        add('✅ متوفرة فقط', () => {
            state.inStock = false;
            document.getElementById('inStockOnly').checked = false;
            applyFilters();
        });
}

/* ===================================================
   MODAL
=================================================== */
function openModal(name, category, price, discount, image, description, isOOS) {
    document.getElementById('modal-name').textContent = name;
    document.getElementById('modal-category').textContent = category;
    document.getElementById('modal-desc').textContent = description || 'لا يوجد وصف لهذا المنتج.';

    /* image */
    const img = document.getElementById('modal-img');
    const ph  = document.getElementById('modal-img-placeholder');
    if (image) {
        img.src = image; img.alt = name;
        img.style.display = 'block'; ph.style.display = 'none';
    } else {
        img.style.display = 'none'; ph.style.display = 'flex';
    }

    /* OOS */
    const oosEl = document.getElementById('modal-oos-overlay');
    oosEl.style.display = isOOS ? 'flex' : 'none';

    /* price */
    const final   = discount > 0 ? (price - price * discount / 100) : price;
    const saving  = price - final;

    document.getElementById('modal-final-price').textContent = '₪' + final.toFixed(2);

    const origEl = document.getElementById('modal-original-price');
    const savEl  = document.getElementById('modal-saving');
    const badge  = document.getElementById('modal-discount-badge');

    if (discount > 0) {
        origEl.textContent = '₪' + price.toFixed(2);
        origEl.style.display = 'block';
        savEl.textContent = 'توفير ₪' + saving.toFixed(2);
        savEl.style.display = 'block';
        badge.textContent  = '🏷️ خصم ' + discount + '%';
        badge.style.display = 'block';
    } else {
        origEl.style.display = 'none';
        savEl.style.display  = 'none';
        badge.style.display  = 'none';
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
