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
