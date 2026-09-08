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
            'cash_amount' => 'nullable|numeric|min:0',
            'bank_amount' => 'nullable|numeric|min:0',
        ]);

        if ((float) $request->cash_amount <= 0 && (float) $request->bank_amount <= 0) {
            return redirect()->back()
                ->withErrors(['cash_amount' => 'يجب إدخال مبلغ أكبر من صفر (نقدي أو بنكي)'])
                ->withInput();
        }

        // تنبيه غير ملزم لو نفس البند اتسجل هالشهر قبل هيك (منع تكرار بالغلط، مثلاً دبل-كليك)
        $duplicateWarning = null;
        $alreadyExists = Obligation::where('expense_type', $request->expense_type)
            ->whereYear('date', \Carbon\Carbon::parse($request->datetime)->year)
            ->whereMonth('date', \Carbon\Carbon::parse($request->datetime)->month)
            ->exists();
        if ($alreadyExists) {
            $duplicateWarning = 'تنبيه: في التزام مسجّل من نفس البند لهالشهر قبل هيك — تأكد إنه مش تكرار.';
        }

        // حفظ البيانات في قاعدة البيانات
        Obligation::create([
            'expense_type' => $request->expense_type,
            'payment_type' => $request->payment_type,
            'cash_amount' => $request->cash_amount,
            'bank_amount' => $request->bank_amount,
            'date' => $request->datetime,
            'detail' => $request->detail,
            'branch_id' => auth()->user()->branch_id,
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('obligations.index')
            ->with('success', 'تم إضافة الالتزام بنجاح')
            ->with('warning', $duplicateWarning);
    }

    public function edit(Obligation $obligation)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('obligations.edit', compact('obligation'));
    }

    public function update(Request $request, Obligation $obligation)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'expense_type' => 'required|string',
            'payment_type' => 'required|string',
            'datetime' => 'required|date',
            'cash_amount' => 'nullable|numeric|min:0',
            'bank_amount' => 'nullable|numeric|min:0',
        ]);

        if ((float) $request->cash_amount <= 0 && (float) $request->bank_amount <= 0) {
            return redirect()->back()
                ->withErrors(['cash_amount' => 'يجب إدخال مبلغ أكبر من صفر (نقدي أو بنكي)'])
                ->withInput();
        }

        $obligation->update([
            'expense_type' => $request->expense_type,
            'payment_type' => $request->payment_type,
            'cash_amount' => $request->cash_amount,
            'bank_amount' => $request->bank_amount,
            'date' => $request->datetime,
            'detail' => $request->detail,
        ]);

        return redirect()->route('obligations.index')->with('success', 'تم تحديث الالتزام بنجاح');
    }

    public function destroy(Obligation $obligation)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $obligation->delete();

        return redirect()->route('obligations.index')->with('success', 'تم حذف الالتزام بنجاح');
    }
}
