<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceDeposit;
use Illuminate\Http\Request;

class MaintenanceDepositController extends Controller
{
    // عرض الأمانات
    public function index()
    {
        // جلب الأمانات من قاعدة البيانات
        $deposits = MaintenanceDeposit::all();

        // عرض الأمانات في الصفحة
        return view('deposits.index', compact('deposits'));
    }

    // عرض نموذج إضافة أمانة جديدة
    public function create()
    {
        return view('deposits.create');
    }

    // حفظ الأمانة الجديدة
    public function store(Request $request)
    {
        // التحقق من البيانات المدخلة
        $validated = $request->validate([
            'piece' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'taken_at' => 'required|date',
            'returned_at' => 'nullable|date',
        ]);

        // إضافة الأمانة إلى قاعدة البيانات
        MaintenanceDeposit::create($validated);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('deposits.index')->with('success', 'تم إضافة الأمانة بنجاح');
    }

    // عرض نموذج تعديل الأمانة
    public function update(Request $request, $id)
    {
        // التحقق من البيانات المدخلة
        $validated = $request->validate([
            'piece' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'taken_at' => 'required|date',
            'returned_at' => 'nullable|date',
        ]);

        // إيجاد الأمانة
        $deposit = MaintenanceDeposit::findOrFail($id);

        // تحديث الأمانة
        $deposit->update($validated);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('deposits.index')->with('success', 'تم تعديل الأمانة بنجاح');
    }

    // عرض نموذج تعديل الأمانة
    public function edit($id)
    {
        // جلب الأمانة من قاعدة البيانات باستخدام الـ ID
        $deposit = MaintenanceDeposit::findOrFail($id);

        // إعادة عرض صفحة التعديل مع بيانات الأمانة
        return view('deposits.edit', compact('deposit'));
    }

    // حذف الأمانة
    public function destroy($id)
    {
        // إيجاد الأمانة التي سيتم حذفها
        $deposit = MaintenanceDeposit::findOrFail($id);

        // حذف الأمانة من قاعدة البيانات
        $deposit->delete();

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('deposits.index')->with('success', 'تم حذف الأمانة بنجاح');
    }
}
