<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleLaptop;
use App\Models\SaleLaptopImage;
use Illuminate\Support\Str;

class SaleLaptopController extends Controller
{
    /**
     * عرض اللابتوبات المتوفرة للزبون مع فلاتر (بحث / ماركة / سعر / توفر / ترتيب)
     */
    public function index(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('laptops.index-admin');
        }

        $query = SaleLaptop::with('mainImage');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhere('processor', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        if ($request->boolean('in_stock')) {
            $query->where('is_out_of_stock', 0);
        }

        match ($request->get('sort', 'price-desc')) {
            'price-asc'  => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'alpha'      => $query->orderBy('name'),
            'latest'     => $query->latest(),
            default      => $query->orderByDesc('price'),
        };

        $laptops    = $query->paginate(12)->withQueryString();
        $brands     = SaleLaptop::distinct()->orderBy('brand')->pluck('brand');
        $minPrice   = (int) floor(SaleLaptop::min('price') ?? 0);
        $maxPrice   = (int) ceil(SaleLaptop::max('price') ?? 1000);
        $totalCount = SaleLaptop::count();

        return view('laptops.index', compact(
            'laptops', 'brands', 'minPrice', 'maxPrice', 'totalCount'
        ));
    }

    public function index_admin()
    {
        $laptops = SaleLaptop::with('mainImage')->latest()->paginate(12);
        return view('laptops.index-admin', compact('laptops'));
    }

    public function create()
    {
        return view('laptops.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'brand'        => 'required|string|max:100',
            'model'        => 'nullable|string|max:100',
            'processor'    => 'required|string|max:150',
            'ram'          => 'required|string|max:50',
            'storage'      => 'required|string|max:50',
            'gpu'          => 'nullable|string|max:150',
            'battery_life' => 'nullable|string|max:100',
            'price'        => 'required|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'description'  => 'nullable|string',
            'images'       => 'nullable|array',
            'images.*'     => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;

        $laptop = SaleLaptop::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('laptops', $imageName, 'public');
                SaleLaptopImage::create([
                    'sale_laptop_id' => $laptop->id,
                    'image'          => 'laptops/' . $imageName,
                ]);
            }
        }

        return redirect()->route('laptops.index-admin')->with('success', 'تم إضافة اللابتوب بنجاح');
    }

    public function edit(SaleLaptop $laptop)
    {
        $laptop->load('images');
        return view('laptops.edit', compact('laptop'));
    }

    public function update(Request $request, SaleLaptop $laptop)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'brand'        => 'required|string|max:100',
            'model'        => 'nullable|string|max:100',
            'processor'    => 'required|string|max:150',
            'ram'          => 'required|string|max:50',
            'storage'      => 'required|string|max:50',
            'gpu'          => 'nullable|string|max:150',
            'battery_life' => 'nullable|string|max:100',
            'price'        => 'required|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'description'  => 'nullable|string',
            'images'       => 'nullable|array',
            'images.*'     => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['discount']        = $validated['discount'] ?? $laptop->discount;
        $validated['is_out_of_stock'] = $request->boolean('is_out_of_stock');

        $laptop->update($validated);

        // إضافة صور جديدة للمعرض (الصور القديمة تبقى إلا إذا تم حذفها يدوياً)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('laptops', $imageName, 'public');
                SaleLaptopImage::create([
                    'sale_laptop_id' => $laptop->id,
                    'image'          => 'laptops/' . $imageName,
                ]);
            }
        }

        return redirect()->route('laptops.index-admin')->with('success', 'تم تحديث اللابتوب بنجاح');
    }

    /**
     * حذف صورة واحدة من معرض صور اللابتوب (تستخدم من صفحة التعديل)
     */
    public function destroyImage(SaleLaptopImage $image)
    {
        $path = storage_path('app/public/' . $image->image);
        if (file_exists($path)) {
            unlink($path);
        }
        $image->delete();

        return back()->with('success', 'تم حذف الصورة');
    }

    public function destroy(SaleLaptop $laptop)
    {
        foreach ($laptop->images as $image) {
            $path = storage_path('app/public/' . $image->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $laptop->delete();

        return redirect()->route('laptops.index-admin')->with('success', 'تم حذف اللابتوب بنجاح');
    }
}
