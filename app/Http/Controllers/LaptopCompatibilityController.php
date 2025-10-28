<?php

namespace App\Http\Controllers;

use App\Models\Laptop;
use App\Models\Part;
use App\Models\PartType;
use Illuminate\Http\Request;

class LaptopCompatibilityController extends Controller
{
    // عرض صفحة المتطابقات
    public function index()
    {
        $laptops = Laptop::with('parts.partType')->get();
        $partTypes = PartType::all();

        return view('compatibility.index', compact('laptops', 'partTypes'));
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
}
