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
