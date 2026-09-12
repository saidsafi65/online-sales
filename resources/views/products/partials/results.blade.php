@php
    $hasFilters = request()->hasAny(['search','category','price_min','price_max','discount','in_stock']);
    $__tenant = app()->bound('currentTenant') ? app('currentTenant') : null;
    $__hasPaymentMethod = $__tenant && $__tenant->hasAnyPaymentGatewayEnabled();
    $__whatsappNumber = $__tenant->contact_whatsapp ?? null;
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
                {{ $product->id }},
                {{ json_encode($product->name) }},
                {{ json_encode($product->category) }},
                {{ $product->price }},
                {{ $product->discount }},
                {{ json_encode($product->image ? asset('storage/'.$product->image) : '') }},
                {{ json_encode($product->description ?? '') }},
                {{ $product->is_out_of_stock ? 'true' : 'false' }}
             )">

            <div class="card-img">
                @if($product->image && file_exists(storage_path('app/public/'.$product->image)))
                    <img src="{{ asset('storage/'.$product->image) }}"
                         alt="{{ $product->name }}" loading="lazy">
                @else
                    <div class="card-img-placeholder">
                        <i class="fas fa-box"></i>
                    </div>
                @endif

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

                @if($product->is_out_of_stock)
                    <div class="oos-overlay">
                        <i class="fas fa-ban"></i>
                        <span>نفذت الكمية</span>
                    </div>
                @endif

                @auth('customer')
                    <form method="POST" action="{{ route('wishlist.toggle', $product) }}" onclick="event.stopPropagation()">
                        @csrf
                        <button type="submit" class="wishlist-btn {{ $wishlistedIds->contains($product->id) ? 'active' : '' }}">
                            <i class="fa{{ $wishlistedIds->contains($product->id) ? 's' : 'r' }} fa-heart"></i>
                        </button>
                    </form>
                @endauth
            </div>

            <div class="card-body">
                <div class="card-category">{{ $product->category }}</div>
                <h3 class="card-name">
                    <a href="{{ route('products.detail', $product) }}" onclick="event.stopPropagation()" style="color:inherit; text-decoration:none;">{{ $product->name }}</a>
                </h3>
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

                @if ($__hasPaymentMethod)
                    @auth('customer')
                        @if(!$product->is_out_of_stock)
                            <button type="button" class="btn-add-cart"
                                    onclick="event.stopPropagation(); addToCart({{ $product->id }}, this)">
                                <i class="fas fa-cart-plus"></i> أضف للسلة
                            </button>
                        @endif
                    @else
                        <a href="{{ route('customer.login') }}" class="btn-add-cart btn-add-cart-guest"
                           onclick="event.stopPropagation()">
                            <i class="fas fa-sign-in-alt"></i> سجل دخول للشراء
                        </a>
                    @endauth
                @elseif ($__whatsappNumber)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $__whatsappNumber) }}?text={{ urlencode('مرحباً، أنا مهتم بمنتج: ' . $product->name) }}"
                       target="_blank" class="btn-add-cart" style="background:#25d366;"
                       onclick="event.stopPropagation()">
                        <i class="fab fa-whatsapp"></i> تواصل واتساب
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>

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
