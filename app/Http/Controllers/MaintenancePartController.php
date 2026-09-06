<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePart;
use Illuminate\Http\Request;

class MaintenancePartController extends Controller
{
    // عرض قائمة الأجهزة وقطعها
    public function index(Request $request)
    {
        $query = MaintenancePart::query();

        // البحث
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // الفلترة حسب الحالة
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $parts = $query->latest()->paginate(10);
        
        return view('maintenance_parts.index', compact('parts'));
    }

    // عرض صفحة إضافة جهاز جديد
    public function create()
    {
        return view('maintenance_parts.create');
    }

    // حفظ جهاز جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'screen' => 'nullable|string|max:255',
            'motherboard' => 'nullable|string|max:255',
            'screen_cover' => 'nullable|string|max:255',
            'battery' => 'nullable|string|max:255',
            'keyboard' => 'nullable|string|max:255',
            'wifi_card' => 'nullable|string|max:255',
            'hard_drive' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'charger' => 'nullable|string|max:255',
            'fan' => 'nullable|string|max:255',
            'other_parts' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:متوفر,غير متوفر,قيد الطلب'
        ], [
            'device_name.required' => 'اسم الجهاز مطلوب',
            'brand.required' => 'العلامة التجارية مطلوبة',
            'model.required' => 'الموديل مطلوب',
            'status.required' => 'الحالة مطلوبة'
        ]);

        MaintenancePart::create($validated);

        return redirect()->route('maintenance_parts.index')
            ->with('success', 'تم إضافة الجهاز وقطعه بنجاح!');
    }

    // عرض تفاصيل جهاز
    public function show(MaintenancePart $maintenancePart)
    {
        return view('maintenance_parts.show', compact('maintenancePart'));
    }

    // عرض صفحة التعديل
    public function edit(MaintenancePart $maintenancePart)
    {
        return view('maintenance_parts.edit', compact('maintenancePart'));
    }

    // تحديث بيانات جهاز
    public function update(Request $request, MaintenancePart $maintenancePart)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'screen' => 'nullable|string|max:255',
            'motherboard' => 'nullable|string|max:255',
            'screen_cover' => 'nullable|string|max:255',
            'battery' => 'nullable|string|max:255',
            'keyboard' => 'nullable|string|max:255',
            'wifi_card' => 'nullable|string|max:255',
            'hard_drive' => 'nullable|string|max:255',
            'ram' => 'nullable|string|max:255',
            'charger' => 'nullable|string|max:255',
            'fan' => 'nullable|string|max:255',
            'other_parts' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:متوفر,غير متوفر,قيد الطلب'
        ], [
            'device_name.required' => 'اسم الجهاز مطلوب',
            'brand.required' => 'العلامة التجارية مطلوبة',
            'model.required' => 'الموديل مطلوب',
            'status.required' => 'الحالة مطلوبة'
        ]);

        $maintenancePart->update($validated);

        return redirect()->route('maintenance_parts.index')
            ->with('success', 'تم تحديث بيانات الجهاز بنجاح!');
    }

    // حذف جهاز
    public function destroy(MaintenancePart $maintenancePart)
    {
        $maintenancePart->delete();

        return redirect()->route('maintenance_parts.index')
            ->with('success', 'تم حذف الجهاز بنجاح!');
    }
}