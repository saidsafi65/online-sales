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
     * عرض جميع المنتجات
     */
    public function index()
    {
        $products = Product::latest()->paginate(12);
        return view('products.index', compact('products'));
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
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // حفظ الصورة بتسمية فريدة
            $image = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            
            // حفظ الصورة في مجلد public/storage/products
            $image->storeAs('products', $imageName, 'public');
            
            $validated['image'] = 'products/' . $imageName;
        } else {
            $validated['image'] = null;
        }

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
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // معالجة الصورة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($product->image) {
                $oldImagePath = storage_path('app/public/' . $product->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // حفظ الصورة الجديدة
            $image = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $validated['image'] = 'products/' . $imageName;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * حذف المنتج
     */
    public function destroy(Product $product)
    {
        // حذف الصورة من التخزين
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