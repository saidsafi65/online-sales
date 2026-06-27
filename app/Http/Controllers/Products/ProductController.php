<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
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

        // ===== البحث بالاسم =====
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
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

        // ===== الترتيب =====
        match ($request->get('sort', 'latest')) {
            'price-asc'  => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'discount'   => $query->orderByDesc('discount'),
            'alpha'      => $query->orderBy('name'),
            default      => $query->latest(),
        };

        $products        = $query->paginate(12)->withQueryString();
        $categories      = Product::distinct()->orderBy('category')->pluck('category');
        $minPrice        = (int) floor(Product::min('price') ?? 0);
        $maxPrice        = (int) ceil(Product::max('price') ?? 1000);
        $discountedCount = Product::where('discount', '>', 0)->count();
        $totalCount      = Product::count();

        return view('products.index', compact(
            'products', 'categories', 'minPrice', 'maxPrice',
            'discountedCount', 'totalCount'
        ));
    }

    public function index_admin()
    {
        $products = Product::latest()->paginate(12);
        return view('products.index-admin', compact('products'));
    }

    /**
     * عرض صفحة إنشاء منتج جديد
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * حفظ المنتج الجديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    /**
     * عرض تفاصيل منتج واحد
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * عرض صفحة تعديل منتج موجود
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * تحديث بيانات المنتج
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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

        $validated['discount']        = $validated['discount'] ?? $product->discount;
        $validated['category']        = $validated['category'] ?? $product->category;
        $validated['is_out_of_stock'] = $request->boolean('is_out_of_stock');

        $product->update($validated);

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
