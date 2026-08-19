@extends('layout.gust')

@section('title', 'البرامج المتوفرة')

@push('styles')
<style>
*, *::before, *::after { box-sizing: border-box; }
:root {
    --brand: #dc2626; --brand-light: #fee2e2; --brand-dark: #991b1b;
    --green: #059669; --green-light: #d1fae5; --amber: #d97706; --amber-light: #fef3c7; --red: #dc2626;
    --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-200: #e2e8f0; --slate-300: #cbd5e1;
    --slate-400: #94a3b8; --slate-500: #64748b; --slate-700: #334155; --slate-900: #0f172a;
    --r-sm: 8px; --r-md: 14px; --r-lg: 20px; --r-xl: 28px;
    --sh-sm: 0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
    --sh-md: 0 4px 16px rgba(0,0,0,.08); --sh-lg: 0 12px 36px rgba(0,0,0,.12); --sh-xl: 0 24px 60px rgba(0,0,0,.18);
    --t: 0.22s cubic-bezier(.4,0,.2,1);
}
.shop-layout { display:grid;grid-template-columns:250px 1fr;gap:1.75rem;align-items:start;padding-bottom:3rem; }
.shop-header { margin-bottom:1.75rem;padding:2rem 2.25rem;border-radius:var(--r-xl);background:linear-gradient(135deg,#450a0a 0%,#991b1b 45%,#dc2626 100%);color:white;position:relative;overflow:hidden; }
.shop-header-inner { position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem; }
.shop-header h1 { font-size:1.9rem;font-weight:900;margin:0;letter-spacing:-0.5px; }
.shop-header p { font-size:1rem;opacity:.75;margin:.35rem 0 0; }
.header-stats { display:flex;gap:1rem; }
.stat-pill { background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:50px;padding:.45rem 1.1rem;font-size:.85rem;font-weight:600;backdrop-filter:blur(6px);display:flex;align-items:center;gap:.5rem; }

.sidebar { position:sticky;top:1.5rem;display:flex;flex-direction:column;gap:1rem; }
.filter-card { background:white;border-radius:var(--r-lg);box-shadow:var(--sh-md);overflow:hidden; }
.filter-card-header { padding:.85rem 1.2rem;background:var(--slate-50);border-bottom:1px solid var(--slate-200);font-size:.78rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:.8px;display:flex;align-items:center;gap:.5rem; }
.filter-card-body { padding:1rem 1.1rem; }
.cat-list { display:flex;flex-direction:column;gap:.2rem; }
.cat-btn { display:flex;align-items:center;gap:.65rem;padding:.65rem .85rem;border-radius:var(--r-sm);cursor:pointer;border:none;background:transparent;width:100%;text-align:right;color:var(--slate-700);font-size:.92rem;font-weight:500;font-family:inherit;transition:background var(--t),color var(--t); }
.cat-btn:hover { background:var(--slate-50);color:var(--brand); }
.cat-btn.active { background:var(--brand-light);color:var(--brand-dark);font-weight:700; }
.cat-icon { width:28px;height:28px;border-radius:var(--r-sm);background:var(--slate-100);display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;transition:background var(--t); }
.cat-btn.active .cat-icon { background:var(--brand);color:white; }
.cat-label { flex:1; }
.cat-count { background:var(--slate-100);color:var(--slate-500);font-size:.73rem;font-weight:700;padding:.18rem .55rem;border-radius:50px; }
.cat-btn.active .cat-count { background:var(--brand);color:white; }
.toggle-row { display:flex;align-items:center;justify-content:space-between;padding:.6rem 0; }
.toggle-label { font-size:.88rem;font-weight:600;color:var(--slate-700); }
.toggle-switch { position:relative;width:42px;height:24px;flex-shrink:0; }
.toggle-switch input { opacity:0;width:0;height:0; }
.toggle-slider { position:absolute;inset:0;background:var(--slate-200);border-radius:50px;cursor:pointer;transition:background var(--t); }
.toggle-slider::before { content:'';position:absolute;width:18px;height:18px;left:3px;top:3px;background:white;border-radius:50%;transition:transform var(--t);box-shadow:0 1px 4px rgba(0,0,0,.2); }
.toggle-switch input:checked + .toggle-slider { background:var(--brand); }
.toggle-switch input:checked + .toggle-slider::before { transform:translateX(18px); }
.clear-btn { display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.65rem;border:1.5px solid var(--slate-200);border-radius:var(--r-sm);background:white;color:var(--slate-500);font-family:inherit;font-weight:600;font-size:.88rem;cursor:pointer;text-decoration:none;transition:all var(--t); }
.clear-btn:hover { border-color:var(--red);color:var(--red);background:#fff5f5; }

.main-col { display:flex;flex-direction:column;gap:1.2rem; }
.toolbar { background:white;border-radius:var(--r-lg);box-shadow:var(--sh-sm);padding:1rem 1.2rem;display:flex;gap:.85rem;align-items:center;border:1px solid var(--slate-200);flex-wrap:wrap; }
.search-wrap { flex:1;min-width:180px;position:relative; }
.search-wrap input { width:100%;padding:.72rem 2.8rem .72rem 1rem;border:1.5px solid var(--slate-200);border-radius:var(--r-md);font-family:inherit;font-size:.93rem;color:var(--slate-700);background:var(--slate-50); }
.search-wrap input:focus { outline:none;border-color:var(--brand);background:white;box-shadow:0 0 0 3px rgba(8,145,178,.1); }
.search-btn { position:absolute;left:.4rem;top:50%;transform:translateY(-50%);width:32px;height:32px;border:none;border-radius:var(--r-sm);background:var(--brand);color:white;cursor:pointer;display:flex;align-items:center;justify-content:center; }
.sort-select { padding:.72rem 1rem;border:1.5px solid var(--slate-200);border-radius:var(--r-md);background:var(--slate-50);font-family:inherit;font-size:.88rem;color:var(--slate-700);font-weight:600;cursor:pointer; }
.results-count { font-size:.84rem;color:var(--slate-400);font-weight:600;white-space:nowrap; }
.results-count strong { color:var(--brand); }
.active-filters { display:flex;flex-wrap:wrap;gap:.45rem;align-items:center; }
.filter-chip { display:inline-flex;align-items:center;gap:.4rem;background:var(--brand-light);color:var(--brand-dark);border-radius:50px;padding:.28rem .8rem;font-size:.8rem;font-weight:700;text-decoration:none;border:1.5px solid #fca5a5; }

.product-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(225px,1fr));gap:1.2rem; }
.product-card { background:white;border-radius:var(--r-lg);box-shadow:var(--sh-sm);border:1px solid var(--slate-200);overflow:hidden;display:flex;flex-direction:column;cursor:pointer;transition:transform var(--t),box-shadow var(--t),border-color var(--t); }
.product-card:hover { transform:translateY(-4px);box-shadow:var(--sh-lg);border-color:#fca5a5; }
.card-img { position:relative;height:170px;overflow:hidden;background:var(--slate-100);flex-shrink:0; }
.card-img img { width:100%;height:100%;object-fit:contain;padding:12px;transition:transform .4s ease;display:block; }
.product-card:hover .card-img img { transform:scale(1.05); }
.card-img-placeholder { width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.75rem;color:var(--slate-300);background:linear-gradient(135deg,var(--slate-100) 0%,#fee2e2 100%); }
.badge-wrap { position:absolute;top:.6rem;right:.6rem;display:flex;flex-direction:column;gap:.3rem;z-index:3; }
.badge { display:inline-flex;align-items:center;gap:.28rem;padding:.28rem .65rem;border-radius:50px;font-size:.72rem;font-weight:800;box-shadow:0 2px 8px rgba(0,0,0,.15); }
.badge-oos { background:rgba(0,0,0,.6);color:white; }
.oos-overlay { position:absolute;inset:0;background:rgba(15,23,42,.48);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.45rem;z-index:4; }
.oos-overlay i { font-size:1.85rem;color:#f87171; }
.oos-overlay span { background:var(--red);color:white;font-weight:800;font-size:.82rem;padding:.3rem .9rem;border-radius:50px; }
.card-body { padding:.95rem 1.05rem 1.1rem;display:flex;flex-direction:column;gap:.4rem;flex:1; }
.card-category { font-size:.7rem;font-weight:700;color:var(--brand);text-transform:uppercase;letter-spacing:.8px; }
.card-name { font-size:.97rem;font-weight:700;color:var(--slate-900);line-height:1.35;margin:0; }
.card-dev { font-size:.78rem;color:var(--slate-400);font-weight:600; }
.card-price-row { margin-top:auto;padding-top:.6rem;display:flex;align-items:center; }
.card-price { font-size:1.1rem;font-weight:900;color:var(--green); }
.card-price.free { color:var(--slate-400);font-size:.9rem; }

.empty-state { grid-column:1/-1;text-align:center;padding:4rem 2rem;color:var(--slate-400); }
.empty-state i { font-size:3.5rem;display:block;margin-bottom:1rem;opacity:.4; }
.empty-state h3 { font-size:1.2rem;color:var(--slate-500);margin:0 0 .4rem; }
.pagination-wrap { display:flex;justify-content:center;padding-top:.5rem; }

#modal { position:fixed;inset:0;z-index:9000;display:none;align-items:center;justify-content:center;padding:1rem; }
#modal.open { display:flex; }
#modal-backdrop { position:absolute;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(6px);animation:fadeIn .22s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
#modal-box {
    position:relative;z-index:1;background:white;border-radius:var(--r-xl);width:100%;max-width:780px;
    max-height:90vh;overflow:hidden;display:grid;grid-template-columns:1fr 1fr;box-shadow:var(--sh-xl);
    animation:slideUp .32s cubic-bezier(.175,.885,.32,1.275);
}
@keyframes slideUp { from{opacity:0;transform:translateY(28px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }

#modal-img-col { position:relative;background:var(--slate-100);min-height:360px;overflow:hidden;display:flex;align-items:center;justify-content:center; }
#modal-img { width:100%;height:100%;max-height:none;object-fit:contain;padding:16px;display:block;background:transparent;border-radius:0;margin:0; }
#modal-img-placeholder { display:none;width:100%;height:100%;min-height:360px;align-items:center;justify-content:center;font-size:5rem;color:var(--slate-300);background:linear-gradient(135deg,var(--slate-100) 0%,#fee2e2 100%); }
#modal-oos-overlay { position:absolute;inset:0;background:rgba(15,23,42,.52);display:none;flex-direction:column;align-items:center;justify-content:center;gap:.7rem; }
#modal-oos-overlay i { font-size:2.5rem;color:#f87171; }
#modal-oos-overlay span { background:var(--red);color:white;font-weight:800;font-size:.95rem;padding:.45rem 1.4rem;border-radius:50px; }

#modal-info-col { padding:1.85rem;display:flex;flex-direction:column;gap:.9rem;overflow-y:auto; }
#modal-info-col::-webkit-scrollbar { width:5px; }
#modal-info-col::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:50px; }
#modal-close { position:absolute;top:.85rem;left:.85rem;width:34px;height:34px;border-radius:50%;border:none;background:rgba(255,255,255,.92);backdrop-filter:blur(4px);color:var(--slate-700);font-size:.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:var(--sh-sm);z-index:10;transition:background var(--t),transform var(--t); }
#modal-close:hover { background:white;transform:scale(1.1); }
#modal-category { font-size:.73rem;font-weight:800;color:var(--brand);text-transform:uppercase;letter-spacing:1px; }
#modal-name { font-size:1.4rem;font-weight:900;color:var(--slate-900);margin:.2rem 0 0; line-height:1.25; }
#modal-dev { font-size:.85rem;color:var(--slate-500); }
.spec-table { display:grid;grid-template-columns:1fr 1fr;gap:.6rem; }
.spec-item { background:var(--slate-50);border:1px solid var(--slate-200);border-radius:var(--r-sm);padding:.6rem .75rem; }
.spec-item .label { font-size:.7rem;color:var(--slate-400);font-weight:700;margin-bottom:.15rem; }
.spec-item .value { font-size:.88rem;color:var(--slate-900);font-weight:700; }
#modal-desc { font-size:.9rem;color:var(--slate-500);line-height:1.85;white-space:pre-wrap; }
#modal-price-box { background:var(--green-light);border:1.5px solid #6ee7b7;border-radius:var(--r-md);padding:1.05rem; }
#modal-price-box .label { font-size:.75rem;font-weight:700;color:var(--slate-400);margin-bottom:.35rem; }
#modal-final-price { font-size:2rem;font-weight:900;color:var(--green);line-height:1; }

@media (max-width:1024px) { .shop-layout { grid-template-columns:220px 1fr;gap:1.25rem; } .product-grid { grid-template-columns:repeat(auto-fill,minmax(195px,1fr)); } }
@media (max-width:768px) {
    .shop-layout { grid-template-columns:1fr; }
    .sidebar { position:relative;top:auto; }
    .shop-header h1 { font-size:1.4rem; }
    .header-stats { display:none; }
    .product-grid { grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.9rem; }
    .spec-table { grid-template-columns:1fr; }
    #modal-box { grid-template-columns:1fr;max-height:88vh; }
    #modal-img-col { min-height:210px; }
    #modal-name { font-size:1.3rem; }
    #modal-final-price { font-size:1.7rem; }
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="shop-header">
        <div class="shop-header-inner">
            <div>
                <h1>🖥️ البرامج المتوفرة</h1>
                <p>تصفح البرامج المتوفرة لدينا بالتفصيل</p>
            </div>
            <div class="header-stats">
                <div class="stat-pill"><i class="fas fa-compact-disc"></i> {{ $totalCount }} برنامج</div>
                <div class="stat-pill"><i class="fas fa-th-large"></i> {{ $categories->count() }} تصنيف</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('software.index') }}" id="filterForm" autocomplete="off">
        <div class="shop-layout">
            <aside class="sidebar">
                <input type="hidden" name="category" id="categoryInput" value="{{ request('category') }}">
                <div class="filter-card">
                    <div class="filter-card-header"><i class="fas fa-layer-group"></i> التصنيفات</div>
                    <div class="filter-card-body">
                        <div class="cat-list">
                            <button type="button" class="cat-btn {{ !request('category') ? 'active' : '' }}" onclick="selectCategory('')">
                                <span class="cat-icon"><i class="fas fa-th"></i></span>
                                <span class="cat-label">كل البرامج</span>
                                <span class="cat-count">{{ $totalCount }}</span>
                            </button>
                            @foreach($categories as $cat)
                                @php $cnt = \App\Models\Software::where('category', $cat)->count(); @endphp
                                <button type="button" class="cat-btn {{ request('category') === $cat ? 'active' : '' }}" onclick="selectCategory('{{ $cat }}')">
                                    <span class="cat-icon"><i class="fas fa-folder"></i></span>
                                    <span class="cat-label">{{ $cat }}</span>
                                    <span class="cat-count">{{ $cnt }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="filter-card">
                    <div class="filter-card-header"><i class="fas fa-sliders-h"></i> خيارات إضافية</div>
                    <div class="filter-card-body">
                        <div class="toggle-row">
                            <span class="toggle-label">✅ متوفر فقط</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <a href="{{ route('software.index') }}" class="clear-btn"><i class="fas fa-redo"></i> إعادة تعيين الفلاتر</a>
            </aside>

            <main class="main-col">
                <div class="toolbar">
                    <div class="search-wrap">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم البرنامج أو الشركة المطورة..."
                               onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('filterForm').submit();}">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                    <select name="sort" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="latest" {{ request('sort','latest') === 'latest' ? 'selected' : '' }}>الأحدث أولاً</option>
                        <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>الاسم أ–ي</option>
                        <option value="price-asc" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>السعر: تصاعدي</option>
                        <option value="price-desc" {{ request('sort') === 'price-desc' ? 'selected' : '' }}>السعر: تنازلي</option>
                    </select>
                    <div class="results-count"><strong>{{ $software->total() }}</strong> نتيجة</div>
                </div>

                @php $hasFilters = request()->hasAny(['search','category','in_stock']); @endphp
                @if($hasFilters)
                <div class="active-filters">
                    @if(request('search'))
                        <a href="{{ route('software.index', request()->except(['search','page'])) }}" class="filter-chip">🔍 {{ request('search') }} <span>✕</span></a>
                    @endif
                    @if(request('category'))
                        <a href="{{ route('software.index', request()->except(['category','page'])) }}" class="filter-chip">📂 {{ request('category') }} <span>✕</span></a>
                    @endif
                    @if(request('in_stock'))
                        <a href="{{ route('software.index', request()->except(['in_stock','page'])) }}" class="filter-chip">✅ متوفر فقط <span>✕</span></a>
                    @endif
                </div>
                @endif

                @if($software->count() > 0)
                <div class="product-grid">
                    @foreach ($software as $item)
                        @php
                            $clean = fn ($v) => $v === null ? null : mb_convert_encoding((string) $v, 'UTF-8', 'UTF-8');
                            $softwareData = [
                                'name' => $clean($item->name),
                                'developer' => $clean($item->developer),
                                'version' => $clean($item->version),
                                'category' => $clean($item->category),
                                'platform' => $clean($item->platform),
                                'license_type' => $clean($item->license_type),
                                'price' => $item->price !== null ? (float) $item->price : null,
                                'description' => $clean($item->description),
                                'image' => $item->image ? asset('storage/'.$item->image) : '',
                                'is_out_of_stock' => (bool) $item->is_out_of_stock,
                            ];
                        @endphp
                        <div class="product-card" onclick="openModal({{ Illuminate\Support\Js::from($softwareData) }})">
                            <div class="card-img">
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" loading="lazy">
                                @else
                                    <div class="card-img-placeholder"><i class="fas fa-compact-disc"></i></div>
                                @endif
                                @if($item->is_out_of_stock)
                                    <div class="badge-wrap"><span class="badge badge-oos"><i class="fas fa-ban"></i> غير متوفر</span></div>
                                    <div class="oos-overlay"><i class="fas fa-ban"></i><span>غير متوفر</span></div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="card-category">{{ $item->category }}</div>
                                <h3 class="card-name">{{ $item->name }}</h3>
                                @if($item->developer)
                                    <div class="card-dev">{{ $item->developer }} @if($item->version) · v{{ $item->version }} @endif</div>
                                @endif
                                <div class="card-price-row">
                                    @if($item->price !== null)
                                        <span class="card-price">₪{{ number_format($item->price, 2) }}</span>
                                    @else
                                        <span class="card-price free">اتصل للسعر</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($software->hasPages())
                <div class="pagination-wrap">{{ $software->links() }}</div>
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
            <img id="modal-img" src="" alt="" style="display:none;">
            <div id="modal-img-placeholder"><i class="fas fa-compact-disc"></i></div>
            <div id="modal-oos-overlay"><i class="fas fa-ban"></i><span>غير متوفر</span></div>
        </div>
        <div id="modal-info-col">
            <div id="modal-category"></div>
            <h2 id="modal-name"></h2>
            <div id="modal-dev"></div>
            <div class="spec-table" id="modal-specs"></div>
            <p id="modal-desc"></p>
            <div id="modal-price-box">
                <div class="label">السعر</div>
                <div id="modal-final-price"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectCategory(cat) {
    document.getElementById('categoryInput').value = cat;
    document.querySelectorAll('input[name="page"]').forEach(i => i.remove());
    document.getElementById('filterForm').submit();
}

function openModal(data) {
    document.getElementById('modal-category').textContent = data.category || '';
    document.getElementById('modal-name').textContent = data.name;
    document.getElementById('modal-dev').textContent = [data.developer, data.version ? ('الإصدار ' + data.version) : null].filter(Boolean).join(' · ');
    document.getElementById('modal-desc').textContent = data.description || 'لا يوجد وصف لهذا البرنامج.';

    const img = document.getElementById('modal-img');
    const ph  = document.getElementById('modal-img-placeholder');
    if (data.image) {
        img.src = data.image; img.alt = data.name;
        img.style.display = 'block'; ph.style.display = 'none';
    } else {
        img.style.display = 'none'; ph.style.display = 'flex';
    }

    document.getElementById('modal-oos-overlay').style.display = data.is_out_of_stock ? 'flex' : 'none';

    const specs = [
        ['نظام التشغيل', data.platform || '—'],
        ['نوع الترخيص', data.license_type || '—'],
    ];
    document.getElementById('modal-specs').innerHTML = specs.map(([label, value]) => `
        <div class="spec-item"><div class="label">${label}</div><div class="value">${value}</div></div>
    `).join('');

    document.getElementById('modal-final-price').textContent = data.price !== null ? ('₪' + data.price.toFixed(2)) : 'اتصل للسعر';

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
