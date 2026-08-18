@extends('layout.gust')

@section('title', 'اللابتوبات المتوفرة')

@push('styles')
<style>
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
    --r-sm: 8px; --r-md: 14px; --r-lg: 20px; --r-xl: 28px;
    --sh-sm: 0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
    --sh-md: 0 4px 16px rgba(0,0,0,.08);
    --sh-lg: 0 12px 36px rgba(0,0,0,.12);
    --sh-xl: 0 24px 60px rgba(0,0,0,.18);
    --t: 0.22s cubic-bezier(.4,0,.2,1);
}

.shop-layout { display:grid;grid-template-columns:270px 1fr;gap:1.75rem;align-items:start;padding-bottom:3rem; }

.shop-header {
    margin-bottom:1.75rem;padding:2rem 2.25rem;border-radius:var(--r-xl);
    background:linear-gradient(135deg,#0f172a 0%,#1e293b 45%,#334155 100%);
    color:white;position:relative;overflow:hidden;
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

.sidebar { position:sticky;top:1.5rem;display:flex;flex-direction:column;gap:1rem; }
.filter-card { background:white;border-radius:var(--r-lg);box-shadow:var(--sh-md);overflow:hidden; }
.filter-card-header {
    padding:.85rem 1.2rem;background:var(--slate-50);border-bottom:1px solid var(--slate-200);
    font-size:.78rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;
    letter-spacing:.8px;display:flex;align-items:center;gap:.5rem;
}
.filter-card-body { padding:1rem 1.1rem; }

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
    padding:.18rem .55rem;border-radius:50px;
}
.cat-btn.active .cat-count { background:var(--brand);color:white; }

.price-range-wrap { display:flex;flex-direction:column;gap:.85rem; }
.price-inputs { display:grid;grid-template-columns:1fr 1fr;gap:.6rem; }
.price-input-group label { display:block;font-size:.73rem;font-weight:600;color:var(--slate-400);margin-bottom:.28rem; }
.price-input-group input {
    width:100%;padding:.5rem .65rem;border:1.5px solid var(--slate-200);border-radius:var(--r-sm);
    font-size:.88rem;font-family:inherit;color:var(--slate-700);font-weight:600;
}
.price-input-group input:focus { outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.price-slider-track { position:relative;height:4px;background:var(--slate-200);border-radius:50px;margin:.5rem 0; }
.price-slider-fill  { position:absolute;height:100%;background:var(--brand);border-radius:50px; }
.price-slider { position:absolute;top:50%;transform:translateY(-50%);width:100%;height:4px;-webkit-appearance:none;appearance:none;background:transparent;cursor:pointer; }
.price-slider::-webkit-slider-thumb {
    -webkit-appearance:none;width:18px;height:18px;border-radius:50%;background:var(--brand);
    border:2.5px solid white;box-shadow:0 2px 8px rgba(79,70,229,.4);cursor:pointer;position:relative;z-index:2;
}
.apply-price-btn {
    width:100%;padding:.62rem;border:none;border-radius:var(--r-sm);background:var(--brand);
    color:white;font-family:inherit;font-weight:700;font-size:.88rem;cursor:pointer;transition:background var(--t);
}
.apply-price-btn:hover { background:var(--brand-dark); }

.toggle-row { display:flex;align-items:center;justify-content:space-between;padding:.6rem 0; }
.toggle-label { font-size:.88rem;font-weight:600;color:var(--slate-700); }
.toggle-switch { position:relative;width:42px;height:24px;flex-shrink:0; }
.toggle-switch input { opacity:0;width:0;height:0; }
.toggle-slider { position:absolute;inset:0;background:var(--slate-200);border-radius:50px;cursor:pointer;transition:background var(--t); }
.toggle-slider::before {
    content:'';position:absolute;width:18px;height:18px;left:3px;top:3px;background:white;
    border-radius:50%;transition:transform var(--t);box-shadow:0 1px 4px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-slider { background:var(--brand); }
.toggle-switch input:checked + .toggle-slider::before { transform:translateX(18px); }

.clear-btn {
    display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.65rem;
    border:1.5px solid var(--slate-200);border-radius:var(--r-sm);background:white;color:var(--slate-500);
    font-family:inherit;font-weight:600;font-size:.88rem;cursor:pointer;text-decoration:none;transition:all var(--t);
}
.clear-btn:hover { border-color:var(--red);color:var(--red);background:#fff5f5; }

.main-col { display:flex;flex-direction:column;gap:1.2rem; }
.toolbar {
    background:white;border-radius:var(--r-lg);box-shadow:var(--sh-sm);padding:1rem 1.2rem;
    display:flex;gap:.85rem;align-items:center;border:1px solid var(--slate-200);flex-wrap:wrap;
}
.search-wrap { flex:1;min-width:180px;position:relative; }
.search-wrap input {
    width:100%;padding:.72rem 2.8rem .72rem 1rem;border:1.5px solid var(--slate-200);border-radius:var(--r-md);
    font-family:inherit;font-size:.93rem;color:var(--slate-700);background:var(--slate-50);
}
.search-wrap input:focus { outline:none;border-color:var(--brand);background:white;box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.search-btn {
    position:absolute;left:.4rem;top:50%;transform:translateY(-50%);width:32px;height:32px;border:none;
    border-radius:var(--r-sm);background:var(--brand);color:white;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
}
.sort-select {
    padding:.72rem 1rem;border:1.5px solid var(--slate-200);border-radius:var(--r-md);
    background:var(--slate-50);font-family:inherit;font-size:.88rem;color:var(--slate-700);font-weight:600;cursor:pointer;
}
.results-count { font-size:.84rem;color:var(--slate-400);font-weight:600;white-space:nowrap; }
.results-count strong { color:var(--brand); }

.active-filters { display:flex;flex-wrap:wrap;gap:.45rem;align-items:center; }
.filter-chip {
    display:inline-flex;align-items:center;gap:.4rem;background:var(--brand-light);color:var(--brand-dark);
    border-radius:50px;padding:.28rem .8rem;font-size:.8rem;font-weight:700;text-decoration:none;border:1.5px solid #c7d2fe;
}

.product-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.2rem; }
.product-card {
    background:white;border-radius:var(--r-lg);box-shadow:var(--sh-sm);border:1px solid var(--slate-200);
    overflow:hidden;display:flex;flex-direction:column;cursor:pointer;transition:transform var(--t),box-shadow var(--t),border-color var(--t);
}
.product-card:hover { transform:translateY(-4px);box-shadow:var(--sh-lg);border-color:#c7d2fe; }
.card-img { position:relative;height:190px;overflow:hidden;background:var(--slate-100);flex-shrink:0; }
.card-img img { width:100%;height:100%;object-fit:contain;padding:8px;transition:transform .4s ease;display:block; }
.product-card:hover .card-img img { transform:scale(1.05); }
.card-img-placeholder {
    width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.75rem;color:var(--slate-300);
    background:linear-gradient(135deg,var(--slate-100) 0%,#e0e7ff 100%);
}
.badge-wrap { position:absolute;top:.6rem;right:.6rem;display:flex;flex-direction:column;gap:.3rem;z-index:3; }
.badge { display:inline-flex;align-items:center;gap:.28rem;padding:.28rem .65rem;border-radius:50px;font-size:.72rem;font-weight:800;box-shadow:0 2px 8px rgba(0,0,0,.15); }
.badge-discount { background:var(--amber);color:white; }
.badge-oos { background:rgba(0,0,0,.6);color:white; }
.oos-overlay { position:absolute;inset:0;background:rgba(15,23,42,.48);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.45rem;z-index:4; }
.oos-overlay i { font-size:1.85rem;color:#f87171; }
.oos-overlay span { background:var(--red);color:white;font-weight:800;font-size:.82rem;padding:.3rem .9rem;border-radius:50px; }

.card-body { padding:.95rem 1.05rem 1.1rem;display:flex;flex-direction:column;gap:.5rem;flex:1; }
.card-brand { font-size:.7rem;font-weight:700;color:var(--brand);text-transform:uppercase;letter-spacing:.8px; }
.card-name { font-size:.97rem;font-weight:700;color:var(--slate-900);line-height:1.35;margin:0; }
.spec-chips { display:flex;flex-wrap:wrap;gap:.3rem; }
.spec-chip { background:var(--slate-100);color:var(--slate-700);font-size:.68rem;font-weight:700;padding:.2rem .55rem;border-radius:6px; }
.card-price-row { margin-top:auto;padding-top:.6rem;display:flex;align-items:flex-end;gap:.45rem; }
.card-price { font-size:1.15rem;font-weight:900;color:var(--green);line-height:1; }
.card-original { font-size:.8rem;color:var(--slate-400);text-decoration:line-through;line-height:1; }
.card-saving { margin-right:auto;font-size:.72rem;font-weight:700;color:var(--amber);background:var(--amber-light);padding:.18rem .5rem;border-radius:50px; }

.empty-state { grid-column:1/-1;text-align:center;padding:4rem 2rem;color:var(--slate-400); }
.empty-state i { font-size:3.5rem;display:block;margin-bottom:1rem;opacity:.4; }
.empty-state h3 { font-size:1.2rem;color:var(--slate-500);margin:0 0 .4rem; }

.pagination-wrap { display:flex;justify-content:center;padding-top:.5rem; }

/* ===== MODAL ===== */
#modal { position:fixed;inset:0;z-index:9000;display:none;align-items:center;justify-content:center;padding:1rem; }
#modal.open { display:flex; }
#modal-backdrop { position:absolute;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(6px);animation:fadeIn .22s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
#modal-box {
    position:relative;z-index:1;background:white;border-radius:var(--r-xl);width:100%;max-width:860px;
    max-height:90vh;overflow:hidden;display:grid;grid-template-columns:1fr 1fr;box-shadow:var(--sh-xl);
    animation:slideUp .32s cubic-bezier(.175,.885,.32,1.275);
}
@keyframes slideUp { from{opacity:0;transform:translateY(28px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }

#modal-img-col { position:relative;background:var(--slate-100);min-height:360px;overflow:hidden;display:flex;flex-direction:column; }
#modal-img { width:100%;height:320px;object-fit:contain;padding:16px;display:block; }
#modal-img-placeholder { display:none;width:100%;height:320px;align-items:center;justify-content:center;font-size:5rem;color:var(--slate-300); }
#modal-thumbs { display:flex;gap:.5rem;padding:.75rem;overflow-x:auto;background:white;border-top:1px solid var(--slate-200); }
#modal-thumbs img { width:56px;height:56px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid transparent;flex-shrink:0; }
#modal-thumbs img.active { border-color:var(--brand); }
#modal-oos-overlay { position:absolute;inset:0;background:rgba(15,23,42,.52);display:none;flex-direction:column;align-items:center;justify-content:center;gap:.7rem; }
#modal-oos-overlay i { font-size:2.5rem;color:#f87171; }
#modal-oos-overlay span { background:var(--red);color:white;font-weight:800;font-size:.95rem;padding:.45rem 1.4rem;border-radius:50px; }

#modal-info-col { padding:1.85rem;display:flex;flex-direction:column;gap:.9rem;overflow-y:auto; }
#modal-info-col::-webkit-scrollbar { width:5px; }
#modal-info-col::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:50px; }
#modal-brand { font-size:.73rem;font-weight:800;color:var(--brand);text-transform:uppercase;letter-spacing:1px; }
#modal-name { font-size:1.4rem;font-weight:900;color:var(--slate-900);margin:0;line-height:1.25; }

.spec-table { display:grid;grid-template-columns:1fr 1fr;gap:.6rem; }
.spec-item { background:var(--slate-50);border:1px solid var(--slate-200);border-radius:var(--r-sm);padding:.6rem .75rem; }
.spec-item .label { font-size:.7rem;color:var(--slate-400);font-weight:700;margin-bottom:.15rem; }
.spec-item .value { font-size:.88rem;color:var(--slate-900);font-weight:700; }

#modal-desc { font-size:.9rem;color:var(--slate-500);line-height:1.85;white-space:pre-wrap; }
#modal-price-box { background:var(--green-light);border:1.5px solid #6ee7b7;border-radius:var(--r-md);padding:1.05rem; }
#modal-price-box .label { font-size:.75rem;font-weight:700;color:var(--slate-400);margin-bottom:.35rem; }
#modal-final-price { font-size:2rem;font-weight:900;color:var(--green);line-height:1; }
#modal-original-price { font-size:.93rem;color:var(--slate-400);text-decoration:line-through;margin-top:.28rem;display:none; }
#modal-saving { font-size:.83rem;font-weight:700;color:var(--amber);margin-top:.35rem;display:none; }
#modal-discount-badge { display:none;background:var(--amber-light);color:var(--amber);border:1.5px solid #fcd34d;border-radius:50px;padding:.45rem 1.1rem;font-weight:800;font-size:.88rem;text-align:center; }
#modal-close {
    position:absolute;top:.85rem;left:.85rem;width:34px;height:34px;border-radius:50%;border:none;
    background:rgba(255,255,255,.92);backdrop-filter:blur(4px);color:var(--slate-700);font-size:.95rem;
    cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:var(--sh-sm);z-index:10;
    transition:background var(--t),transform var(--t);
}
#modal-close:hover { background:white;transform:scale(1.1); }

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
    #modal-box { grid-template-columns:1fr;max-height:88vh; }
    #modal-img-col { min-height:210px; }
    #modal-img, #modal-img-placeholder { height:210px; }
    .spec-table { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')
<div class="container">

    <div class="shop-header">
        <div class="shop-header-inner">
            <div>
                <h1>💻 اللابتوبات المتوفرة</h1>
                <p>تشكيلة لابتوبات بمواصفات مختلفة تناسب احتياجك</p>
            </div>
            <div class="header-stats">
                <div class="stat-pill"><i class="fas fa-laptop"></i> {{ $totalCount }} لابتوب</div>
                <div class="stat-pill"><i class="fas fa-copyright"></i> {{ $brands->count() }} ماركة</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('laptops.index') }}" id="filterForm" autocomplete="off">
        <div class="shop-layout">

            <aside class="sidebar">
                <input type="hidden" name="brand" id="brandInput" value="{{ request('brand') }}">

                <div class="filter-card">
                    <div class="filter-card-header"><i class="fas fa-copyright"></i> الماركة</div>
                    <div class="filter-card-body">
                        <div class="cat-list">
                            <button type="button" class="cat-btn {{ !request('brand') ? 'active' : '' }}" onclick="selectBrand('')">
                                <span class="cat-icon"><i class="fas fa-th"></i></span>
                                <span class="cat-label">كل الماركات</span>
                                <span class="cat-count">{{ $totalCount }}</span>
                            </button>
                            @foreach($brands as $brand)
                                @php $cnt = \App\Models\SaleLaptop::where('brand', $brand)->count(); @endphp
                                <button type="button" class="cat-btn {{ request('brand') === $brand ? 'active' : '' }}" onclick="selectBrand('{{ $brand }}')">
                                    <span class="cat-icon"><i class="fas fa-laptop"></i></span>
                                    <span class="cat-label">{{ $brand }}</span>
                                    <span class="cat-count">{{ $cnt }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="filter-card">
                    <div class="filter-card-header"><i class="fas fa-shekel-sign"></i> نطاق السعر</div>
                    <div class="filter-card-body">
                        <div class="price-range-wrap">
                            <div class="price-inputs">
                                <div class="price-input-group">
                                    <label>من (₪)</label>
                                    <input type="number" name="price_min" id="priceMin" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ request('price_min', $minPrice) }}" oninput="syncSlider()">
                                </div>
                                <div class="price-input-group">
                                    <label>إلى (₪)</label>
                                    <input type="number" name="price_max" id="priceMax" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ request('price_max', $maxPrice) }}" oninput="syncSlider()">
                                </div>
                            </div>
                            <div class="price-slider-track">
                                <div class="price-slider-fill" id="sliderFill"></div>
                                <input class="price-slider" type="range" id="sliderMin" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ request('price_min', $minPrice) }}" oninput="onSliderMin()">
                                <input class="price-slider" type="range" id="sliderMax" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ request('price_max', $maxPrice) }}" oninput="onSliderMax()">
                            </div>
                            <button type="submit" class="apply-price-btn"><i class="fas fa-check me-1"></i> تطبيق النطاق</button>
                        </div>
                    </div>
                </div>

                <div class="filter-card">
                    <div class="filter-card-header"><i class="fas fa-sliders-h"></i> خيارات إضافية</div>
                    <div class="filter-card-body">
                        <div class="toggle-row">
                            <span class="toggle-label">✅ متوفرة فقط</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <a href="{{ route('laptops.index') }}" class="clear-btn"><i class="fas fa-redo"></i> إعادة تعيين الفلاتر</a>
            </aside>

            <main class="main-col">
                <div class="toolbar">
                    <div class="search-wrap">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم اللابتوب أو الماركة أو المعالج..."
                               onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('filterForm').submit();}">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                    <select name="sort" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="price-desc" {{ request('sort','price-desc') === 'price-desc' ? 'selected' : '' }}>السعر: الأعلى أولاً</option>
                        <option value="price-asc" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>السعر: الأقل أولاً</option>
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>الأحدث أولاً</option>
                        <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>الاسم أ–ي</option>
                    </select>
                    <div class="results-count"><strong>{{ $laptops->total() }}</strong> نتيجة</div>
                </div>

                @php $hasFilters = request()->hasAny(['search','brand','price_min','price_max','in_stock']); @endphp
                @if($hasFilters)
                <div class="active-filters">
                    @if(request('search'))
                        <a href="{{ route('laptops.index', request()->except(['search','page'])) }}" class="filter-chip">🔍 {{ request('search') }} <span>✕</span></a>
                    @endif
                    @if(request('brand'))
                        <a href="{{ route('laptops.index', request()->except(['brand','page'])) }}" class="filter-chip">🏷️ {{ request('brand') }} <span>✕</span></a>
                    @endif
                    @if(request('in_stock'))
                        <a href="{{ route('laptops.index', request()->except(['in_stock','page'])) }}" class="filter-chip">✅ متوفرة فقط <span>✕</span></a>
                    @endif
                </div>
                @endif

                @if($laptops->count() > 0)
                <div class="product-grid">
                    @foreach ($laptops as $laptop)
                        @php
                            $finalPrice = $laptop->discount > 0 ? $laptop->price * (1 - $laptop->discount / 100) : $laptop->price;
                            $saving = $laptop->price - $finalPrice;
                            $images = $laptop->images()->pluck('image');
                            $clean = fn ($v) => $v === null ? null : mb_convert_encoding((string) $v, 'UTF-8', 'UTF-8');
                            $laptopData = [
                                'name' => $clean($laptop->name),
                                'brand' => $clean($laptop->brand),
                                'model' => $clean($laptop->model),
                                'processor' => $clean($laptop->processor),
                                'ram' => $clean($laptop->ram),
                                'storage' => $clean($laptop->storage),
                                'gpu' => $clean($laptop->gpu),
                                'battery_life' => $clean($laptop->battery_life),
                                'price' => (float) $laptop->price,
                                'discount' => (float) $laptop->discount,
                                'description' => $clean($laptop->description),
                                'is_out_of_stock' => (bool) $laptop->is_out_of_stock,
                                'images' => $images->map(fn ($img) => asset('storage/'.$img)),
                            ];
                        @endphp
                        <div class="product-card" onclick="openModal({{ Illuminate\Support\Js::from($laptopData) }})">
                            <div class="card-img">
                                @if($laptop->mainImage)
                                    <img src="{{ asset('storage/'.$laptop->mainImage->image) }}" alt="{{ $laptop->name }}" loading="lazy">
                                @else
                                    <div class="card-img-placeholder"><i class="fas fa-laptop"></i></div>
                                @endif
                                <div class="badge-wrap">
                                    @if($laptop->discount > 0)
                                        <span class="badge badge-discount"><i class="fas fa-tag"></i> {{ $laptop->discount }}% خصم</span>
                                    @endif
                                    @if($laptop->is_out_of_stock)
                                        <span class="badge badge-oos"><i class="fas fa-ban"></i> نفذت الكمية</span>
                                    @endif
                                </div>
                                @if($laptop->is_out_of_stock)
                                    <div class="oos-overlay"><i class="fas fa-ban"></i><span>نفذت الكمية</span></div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="card-brand">{{ $laptop->brand }}</div>
                                <h3 class="card-name">{{ $laptop->name }}</h3>
                                <div class="spec-chips">
                                    <span class="spec-chip">{{ $laptop->processor }}</span>
                                    <span class="spec-chip">{{ $laptop->ram }}</span>
                                    <span class="spec-chip">{{ $laptop->storage }}</span>
                                </div>
                                <div class="card-price-row">
                                    <div>
                                        <div class="card-price">₪{{ number_format($finalPrice, 2) }}</div>
                                        @if($laptop->discount > 0)
                                            <div class="card-original">₪{{ number_format($laptop->price, 2) }}</div>
                                        @endif
                                    </div>
                                    @if($laptop->discount > 0)
                                        <span class="card-saving">وفّر ₪{{ number_format($saving, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($laptops->hasPages())
                <div class="pagination-wrap">{{ $laptops->links() }}</div>
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

<div id="modal">
    <div id="modal-backdrop" onclick="closeModal()"></div>
    <div id="modal-box">
        <button id="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        <div id="modal-img-col">
            <img id="modal-img" src="" alt="">
            <div id="modal-img-placeholder"><i class="fas fa-laptop"></i></div>
            <div id="modal-oos-overlay"><i class="fas fa-ban"></i><span>نفذت الكمية</span></div>
            <div id="modal-thumbs"></div>
        </div>
        <div id="modal-info-col">
            <div id="modal-brand"></div>
            <h2 id="modal-name"></h2>
            <div class="spec-table" id="modal-specs"></div>
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
const PRICE_ABS_MIN = {{ $minPrice }};
const PRICE_ABS_MAX = {{ $maxPrice }};

function selectBrand(brand) {
    document.getElementById('brandInput').value = brand;
    document.querySelectorAll('input[name="page"]').forEach(i => i.remove());
    document.getElementById('filterForm').submit();
}

function onSliderMin() {
    const sMin = document.getElementById('sliderMin'), sMax = document.getElementById('sliderMax');
    if (parseInt(sMin.value) > parseInt(sMax.value)) sMin.value = sMax.value;
    document.getElementById('priceMin').value = sMin.value;
    updateSliderFill();
}
function onSliderMax() {
    const sMin = document.getElementById('sliderMin'), sMax = document.getElementById('sliderMax');
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
    const pct = v => ((v - PRICE_ABS_MIN) / (PRICE_ABS_MAX - PRICE_ABS_MIN)) * 100;
    const fill = document.getElementById('sliderFill');
    fill.style.left = pct(min) + '%';
    fill.style.width = (pct(max) - pct(min)) + '%';
}
updateSliderFill();

let currentImages = [];
function setModalImage(idx) {
    const img = document.getElementById('modal-img');
    const ph  = document.getElementById('modal-img-placeholder');
    if (currentImages[idx]) {
        img.src = currentImages[idx]; img.style.display = 'block'; ph.style.display = 'none';
    } else {
        img.style.display = 'none'; ph.style.display = 'flex';
    }
    document.querySelectorAll('#modal-thumbs img').forEach((t, i) => t.classList.toggle('active', i === idx));
}

function openModal(data) {
    document.getElementById('modal-brand').textContent = data.brand;
    document.getElementById('modal-name').textContent  = data.name + (data.model ? ' - ' + data.model : '');
    document.getElementById('modal-desc').textContent  = data.description || 'لا يوجد وصف لهذا اللابتوب.';

    const specs = [
        ['المعالج', data.processor],
        ['الرام', data.ram],
        ['الهارد', data.storage],
        ['كرت الشاشة', data.gpu || '—'],
        ['البطارية', data.battery_life || '—'],
    ];
    document.getElementById('modal-specs').innerHTML = specs.map(([label, value]) => `
        <div class="spec-item"><div class="label">${label}</div><div class="value">${value}</div></div>
    `).join('');

    currentImages = data.images && data.images.length ? data.images : [];
    document.getElementById('modal-thumbs').innerHTML = currentImages.map((src, i) =>
        `<img src="${src}" onclick="setModalImage(${i})">`
    ).join('');
    setModalImage(0);

    document.getElementById('modal-oos-overlay').style.display = data.is_out_of_stock ? 'flex' : 'none';

    const final  = data.discount > 0 ? data.price * (1 - data.discount / 100) : data.price;
    const saving = data.price - final;
    document.getElementById('modal-final-price').textContent = '₪' + final.toFixed(2);

    const origEl = document.getElementById('modal-original-price');
    const savEl  = document.getElementById('modal-saving');
    const badgeEl = document.getElementById('modal-discount-badge');
    if (data.discount > 0) {
        origEl.textContent = '₪' + data.price.toFixed(2); origEl.style.display = 'block';
        savEl.textContent = 'توفير ₪' + saving.toFixed(2); savEl.style.display = 'block';
        badgeEl.textContent = '🏷️ خصم ' + data.discount + '%'; badgeEl.style.display = 'block';
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
