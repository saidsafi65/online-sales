<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Software;
use Illuminate\Support\Str;

class SoftwareController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('software.index-admin');
        }

        $query = Software::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('developer', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('in_stock')) {
            $query->where('is_out_of_stock', 0);
        }

        match ($request->get('sort', 'latest')) {
            'alpha'      => $query->orderBy('name'),
            'price-asc'  => $query->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            default      => $query->latest(),
        };

        $software   = $query->paginate(12)->withQueryString();
        $categories = Software::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $totalCount = Software::count();

        return view('software.index', compact('software', 'categories', 'totalCount'));
    }

    public function index_admin()
    {
        $software = Software::latest()->paginate(12);
        return view('software.index-admin', compact('software'));
    }

    public function create()
    {
        return view('software.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'developer'    => 'nullable|string|max:150',
            'version'      => 'nullable|string|max:50',
            'category'     => 'nullable|string|max:100',
            'platform'     => 'nullable|string|max:100',
            'license_type' => 'nullable|string|max:100',
            'price'        => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('software', $imageName, 'public');
            $validated['image'] = 'software/' . $imageName;
        }

        $validated['category'] = $validated['category'] ?? 'عام';

        Software::create($validated);

        return redirect()->route('software.index-admin')->with('success', 'تم إضافة البرنامج بنجاح');
    }

    public function edit(Software $software)
    {
        return view('software.edit', compact('software'));
    }

    public function update(Request $request, Software $software)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'developer'    => 'nullable|string|max:150',
            'version'      => 'nullable|string|max:50',
            'category'     => 'nullable|string|max:100',
            'platform'     => 'nullable|string|max:100',
            'license_type' => 'nullable|string|max:100',
            'price'        => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($software->image) {
                $oldPath = storage_path('app/public/' . $software->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $image     = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('software', $imageName, 'public');
            $validated['image'] = 'software/' . $imageName;
        }

        $validated['category']        = $validated['category'] ?? $software->category;
        $validated['is_out_of_stock'] = $request->boolean('is_out_of_stock');

        $software->update($validated);

        return redirect()->route('software.index-admin')->with('success', 'تم تحديث البرنامج بنجاح');
    }

    public function destroy(Software $software)
    {
        if ($software->image) {
            $path = storage_path('app/public/' . $software->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $software->delete();

        return redirect()->route('software.index-admin')->with('success', 'تم حذف البرنامج بنجاح');
    }
}
