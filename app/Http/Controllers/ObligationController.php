<?php

namespace App\Http\Controllers;

use App\Models\Obligation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ObligationController extends Controller
{
    // عرض صفحة الالتزامات مع التصفية
    public function index(Request $request)
    {
        $query = Obligation::query();

        // تصفية نوع التكاليف إذا تم تحديده
        if ($request->has('expense_type') && $request->expense_type != '') {
            $query->where('expense_type', $request->expense_type);
        }

        // تصفية تاريخ الالتزام إذا تم تحديده
        if ($request->has('date') && $request->date != '') {
            // تصفية باستخدام التاريخ الكامل
            $query->whereDate('date', Carbon::parse($request->date)->format('Y-m-d'));
        }

        // تصفية طريقة الدفع إذا تم تحديدها
        if ($request->has('payment_type') && $request->payment_type != '') {
            $query->where('payment_type', $request->payment_type);
        }

        // الحصول على التزامات المحل مع التصفية أو الترتيب حسب الحاجة
        $obligations = $query->latest()->paginate(10);

        return view('obligations.index', compact('obligations'));
    }

    // عرض صفحة إضافة التزام جديد
    public function create()
    {
        return view('obligations.create');  // تأكد من أن الصفحة 'obligations.create' موجودة
    }

    // دالة store لحفظ البيانات في قاعدة البيانات
    public function store(Request $request)
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'expense_type' => 'required|string',
            'payment_type' => 'required|string',
            'datetime' => 'required|date',
        ]);

        // حفظ البيانات في قاعدة البيانات
        Obligation::create([
            'expense_type' => $request->expense_type,
            'payment_type' => $request->payment_type,
            'cash_amount' => $request->cash_amount,
            'bank_amount' => $request->bank_amount,
            'date' => $request->datetime,
            'detail' => $request->detail,
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('obligations.index')->with('success', 'تم إضافة الالتزام بنجاح');
    }
}
