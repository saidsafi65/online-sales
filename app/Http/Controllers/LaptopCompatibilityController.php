<?php

namespace App\Http\Controllers;

use App\Models\Laptop;
use App\Models\Part;
use App\Models\PartType;
use Illuminate\Http\Request;

class LaptopCompatibilityController extends Controller
{
    // الماركات الرئيسية اللي بدنا الشجرة تظهرها دايماً، حتى لو ما فيها أجهزة مضافة لسا
    public const MAIN_BRANDS = ['HP', 'Lenovo', 'MSI', 'Acer', 'Asus'];

    // عرض صفحة المتطابقات، مقسّمة كشجرة حسب الماركة
    public function index()
    {
        $laptops = Laptop::with('parts.partType')->get();
        $partTypes = PartType::all();

        $grouped = $laptops->groupBy(function ($laptop) {
            return static::normalizeBrand($laptop->brand);
        });

        // نضمن ظهور الماركات الخمس دايماً كأقسام بالشجرة، حتى لو فاضية
        $brandTree = [];
        foreach (self::MAIN_BRANDS as $brand) {
            $brandTree[$brand] = $grouped->get($brand, collect());
        }
        // أي ماركة تانية (Dell، أو غير مصنّفة) تترتب بعد الخمسة الرئيسية
        foreach ($grouped as $brand => $items) {
            if (! in_array($brand, self::MAIN_BRANDS, true)) {
                $brandTree[$brand] = $items;
            }
        }

        return view('compatibility.index', compact('brandTree', 'partTypes'));
    }

    // توحيد كتابة اسم الماركة (hp / Hp / HP -> HP) حتى تتجمع صح بالشجرة
    public static function normalizeBrand(?string $brand): string
    {
        $brand = trim((string) $brand);
        foreach (self::MAIN_BRANDS as $mainBrand) {
            if (strcasecmp($brand, $mainBrand) === 0) {
                return $mainBrand;
            }
        }

        return $brand !== '' ? $brand : 'أخرى';
    }

    // بحث سريع بالموديل/الماركة — يرجع الأجهزة المطابقة وقطعها مباشرة (AJAX)
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json(['success' => true, 'laptops' => []]);
        }

        $laptops = Laptop::with('parts.partType')
            ->where(function ($query) use ($q) {
                $query->where('brand', 'like', "%{$q}%")
                    ->orWhere('model', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('compatibility.partials.search-results', compact('laptops'))->render(),
        ]);
    }

    // عرض تفاصيل جهاز معين
    public function show($id)
    {
        $laptop = Laptop::with(['parts.partType', 'parts.laptops'])->findOrFail($id);
        $partTypes = PartType::all();

        // الحصول على الأجهزة المتوافقة لكل نوع قطعة
        $compatibilityData = [];
        foreach ($partTypes as $partType) {
            $part = $laptop->getPartByType($partType->id);
            if ($part) {
                $compatibilityData[$partType->id] = [
                    'part' => $part,
                    'compatible_laptops' => $part->allCompatibleLaptops(),
                ];
            }
        }

        return view('compatibility.show', compact('laptop', 'partTypes', 'compatibilityData'));
    }

    // API للحصول على الأجهزة المتوافقة بناءً على نوع القطعة
    public function getCompatibleLaptops(Request $request)
    {
        $laptopId = $request->laptop_id;
        $partTypeId = $request->part_type_id;

        $laptop = Laptop::findOrFail($laptopId);
        $part = $laptop->getPartByType($partTypeId);

        if (! $part) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد قطعة من هذا النوع في الجهاز',
            ]);
        }

        $compatibleLaptops = $part->allCompatibleLaptops();

        return response()->json([
            'success' => true,
            'part' => $part,
            'compatible_laptops' => $compatibleLaptops,
        ]);
    }

    // إضافة توافق جديد
    public function addCompatibility(Request $request)
    {
        $request->validate([
            'laptop_id' => 'required|exists:laptops,id',
            'part_id' => 'required|exists:parts,id',
            'compatible_laptop_id' => 'required|exists:laptops,id',
            'notes' => 'nullable|string',
        ]);

        $part = Part::findOrFail($request->part_id);

        $part->compatibleLaptops()->syncWithoutDetaching([
            $request->compatible_laptop_id => [
                'verified' => false,
                'notes' => $request->notes,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة التوافق بنجاح',
        ]);
    }

    // حذف توافق
    public function removeCompatibility(Request $request)
    {
        $request->validate([
            'part_id' => 'required|exists:parts,id',
            'compatible_laptop_id' => 'required|exists:laptops,id',
        ]);

        $part = Part::findOrFail($request->part_id);
        $part->compatibleLaptops()->detach($request->compatible_laptop_id);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التوافق بنجاح',
        ]);
    }

    // إدارة الأجهزة (CRUD)
    public function manageLaptops()
    {
        $laptops = Laptop::with('parts')->paginate(20);
        $partTypes = PartType::all();

        return view('compatibility.manage_laptops', compact('laptops', 'partTypes'));
    }

    // إضافة جهاز جديد
    public function storeLaptop(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['brand', 'model', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('laptops', 'public');
        }

        $laptop = Laptop::create($data);

        return redirect()->back()->with('success', 'تم إضافة الجهاز بنجاح');
    }

    // ربط قطعة بجهاز
    public function attachPart(Request $request)
    {
        $request->validate([
            'laptop_id' => 'required|exists:laptops,id',
            'part_id' => 'required|exists:parts,id',
            'is_original' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $laptop = Laptop::findOrFail($request->laptop_id);

        $laptop->parts()->syncWithoutDetaching([
            $request->part_id => [
                'is_original' => $request->is_original ?? true,
                'notes' => $request->notes,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم ربط القطعة بالجهاز بنجاح',
        ]);
    }

    // فك ربط قطعة عن جهاز (بدون حذف القطعة نفسها)
    public function detachPart(Request $request)
    {
        $request->validate([
            'laptop_id' => 'required|exists:laptops,id',
            'part_id' => 'required|exists:parts,id',
        ]);

        $laptop = Laptop::findOrFail($request->laptop_id);
        $laptop->parts()->detach($request->part_id);

        return response()->json([
            'success' => true,
            'message' => 'تم فك ربط القطعة عن الجهاز',
        ]);
    }

    // إنشاء قطعة جديدة (SKU) — القطعة ممكن تنربط فوراً بجهاز إذا انبعث laptop_id معها
    public function storePart(Request $request)
    {
        $request->validate([
            'part_type_id' => 'required|exists:part_types,id',
            'part_number' => 'required|string|max:255|unique:parts,part_number',
            'price' => 'nullable|numeric|min:0',
            'laptop_id' => 'nullable|exists:laptops,id',
            'spec_keys' => 'nullable|array',
            'spec_keys.*' => 'nullable|string|max:100',
            'spec_values' => 'nullable|array',
            'spec_values.*' => 'nullable|string|max:255',
        ]);

        $specifications = [];
        foreach ($request->input('spec_keys', []) as $index => $key) {
            $key = trim((string) $key);
            $value = trim((string) ($request->input('spec_values')[$index] ?? ''));
            if ($key !== '' && $value !== '') {
                $specifications[$key] = $value;
            }
        }

        $part = Part::create([
            'part_type_id' => $request->part_type_id,
            'part_number' => $request->part_number,
            'price' => $request->price,
            'specifications' => $specifications,
        ]);

        if ($request->filled('laptop_id')) {
            $laptop = Laptop::findOrFail($request->laptop_id);
            $laptop->parts()->syncWithoutDetaching([
                $part->id => ['is_original' => true, 'notes' => null],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة القطعة بنجاح',
            'part' => $part->load('partType'),
        ]);
    }

    // بيانات لوحة "إدارة القطع" لجهاز معيّن (AJAX) — قطعه الحالية + نماذج ربط/إضافة قطعة
    public function partsPanel($id)
    {
        $laptop = Laptop::with('parts.partType')->findOrFail($id);
        $partTypes = PartType::all();

        return response()->json([
            'success' => true,
            'html' => view('compatibility.partials.parts-panel', compact('laptop', 'partTypes'))->render(),
        ]);
    }
}
