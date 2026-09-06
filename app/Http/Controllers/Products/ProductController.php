<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * عرض جميع المنتجات مع دعم الفلاتر من السيرفر
     */
    public function index(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('products.index-admin');
        }

        $query = Product::query();

        // ===== البحث بالاسم أو التصنيف =====
if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function ($q) use ($search) {
        $q->where('name', 'like', '%' . $search . '%')
          ->orWhere('category', 'like', '%' . $search . '%');
    });
}

        // ===== فلتر التصنيف =====
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // ===== نطاق السعر =====
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        // ===== العروض فقط =====
        if ($request->boolean('discount')) {
            $query->where('discount', '>', 0);
        }

        // ===== المتوفرة فقط =====
        if ($request->boolean('in_stock')) {
            $query->where('is_out_of_stock', 0);
        }

        // ===== نفذت الكمية دايماً بآخر القائمة، بغض النظر عن الترتيب المختار =====
        $query->orderBy('is_out_of_stock');

        // ===== الترتيب =====
        if ($request->get('sort', 'latest') === 'rating') {
            $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
        } else {
            match ($request->get('sort', 'latest')) {
                'price-asc'  => $query->orderBy('price'),
                'price-desc' => $query->orderByDesc('price'),
                'discount'   => $query->orderByDesc('discount'),
                'alpha'      => $query->orderBy('name'),
                default      => $query->latest(),
            };
        }

        $products = $query->paginate(12)->withQueryString();
        $minPrice = (int) floor(Product::min('price') ?? 0);
        $maxPrice = (int) ceil(Product::max('price') ?? 1000);

        $wishlistedIds = auth('customer')->check()
            ? \App\Models\Wishlist::where('customer_id', auth('customer')->id())->pluck('product_id')
            : collect();

        // الفلتر بيرجع الـ HTML الجاهز لجزء النتائج بس (بدون الصفحة كاملة) لما
        // يوصل طلب AJAX من shop-filters.js — نفس الـ view المستخدم جوا الصفحة
        // الكاملة، فمافي احتمال يصير فرق بين الاثنين.
        if ($request->ajax()) {
            return view('products.partials.results', compact('products', 'minPrice', 'maxPrice', 'wishlistedIds'));
        }

        // القيم يلي مستخدمة بالسايدبار بس (مش بجزء النتائج) — ما لازم نحسبها
        // إلا لما نرجع الصفحة كاملة، حتى ما نضيّع وقت بكل ضغطة فلتر.
        $categories = Product::selectRaw('category, COUNT(*) as cnt')
            ->groupBy('category')
            ->orderBy('category')
            ->pluck('cnt', 'category');
        $discountedCount = Product::where('discount', '>', 0)->count();
        $totalCount      = Product::count();

        return view('products.index', compact(
            'products', 'categories', 'minPrice', 'maxPrice',
            'discountedCount', 'totalCount', 'wishlistedIds'
        ));
    }

    public function index_admin(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('category', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Product::distinct()->orderBy('category')->pluck('category');

        return view('products.index-admin', compact('products', 'categories'));
    }

    /**
     * عرض صفحة إنشاء منتج جديد
     */
    public function create()
    {
        $catalogItems = \App\Models\CatalogItem::orderBy('product')->orderBy('type')->get();

        return view('products.create', compact('catalogItems'));
    }

    /**
     * حفظ المنتج الجديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'price'           => 'required|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0|max:100',
            'category'        => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'catalog_item_id' => 'nullable|exists:catalog_items,id',
            'quantity'        => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $validated['image'] = 'products/' . $imageName;
        } else {
            $validated['image'] = null;
        }

        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['category'] = $validated['category'] ?? 'عام';
        $validated['quantity'] = $validated['quantity'] ?? 0;
        $validated['branch_id'] = auth()->user()->branch_id;

        // إذا اختار منتج مربوط بعنصر كتالوج، حالة "نفذت الكمية" بتتحدد تلقائيًا من كميته الحالية
        if (! empty($validated['catalog_item_id'])) {
            $catalogItem = \App\Models\CatalogItem::find($validated['catalog_item_id']);
            $validated['is_out_of_stock'] = $catalogItem ? ((int) $catalogItem->quantity) <= 0 : false;
        } else {
            $validated['is_out_of_stock'] = $validated['quantity'] <= 0;
        }

        $product = Product::create($validated);

        NotificationService::syncStock($product);

        return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    /**
     * عرض تفاصيل منتج واحد
     */
    public function show(Product $product)
    {
        $reviews = $product->reviews()->with('customer')->latest()->get();

        $isWishlisted = false;
        $canReview = false;
        $myReview = null;

        if (auth('customer')->check()) {
            $customerId = auth('customer')->id();

            $isWishlisted = \App\Models\Wishlist::where('customer_id', $customerId)
                ->where('product_id', $product->id)->exists();

            $myReview = $reviews->firstWhere('customer_id', $customerId);

            $canReview = \App\Models\OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($q) use ($customerId) {
                    $q->where('customer_id', $customerId)
                        ->whereIn('status', \App\Models\Order::STATUS_COUNTS_AS_PURCHASED);
                })->exists();
        }

        return view('products.show', compact('product', 'reviews', 'isWishlisted', 'canReview', 'myReview'));
    }

    /**
     * عرض صفحة تعديل منتج موجود
     */
    public function edit(Product $product)
    {
        $catalogItems = \App\Models\CatalogItem::orderBy('product')->orderBy('type')->get();

        return view('products.edit', compact('product', 'catalogItems'));
    }

    /**
     * تحديث بيانات المنتج
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'price'           => 'required|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0|max:100',
            'category'        => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'catalog_item_id' => 'nullable|exists:catalog_items,id',
            'quantity'        => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                $oldImagePath = storage_path('app/public/' . $product->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $image     = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $validated['image'] = 'products/' . $imageName;
        }

        $validated['discount'] = $validated['discount'] ?? $product->discount;
        $validated['category'] = $validated['category'] ?? $product->category;
        $validated['quantity'] = $validated['quantity'] ?? $product->quantity;

        if (! empty($validated['catalog_item_id'])) {
            // مربوط بعنصر كتالوج: حالة "نفذت الكمية" بتتحدد تلقائيًا من كميته الحالية،
            // مش من زر التبديل اليدوي.
            $catalogItem = \App\Models\CatalogItem::find($validated['catalog_item_id']);
            $validated['is_out_of_stock'] = $catalogItem ? ((int) $catalogItem->quantity) <= 0 : false;
        } else {
            $validated['catalog_item_id'] = null;
            $validated['is_out_of_stock'] = $validated['quantity'] <= 0 ? true : $request->boolean('is_out_of_stock');
        }

        $product->update($validated);

        NotificationService::syncStock($product->fresh());

        return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * حذف المنتج
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            $imagePath = storage_path('app/public/' . $product->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح');
    }
} 