<?php
// ============================================================
// FILE 1: app/Http/Controllers/DebtController.php
// FIX: index() كان يبني $query بالفلتر ثم يتجاهله ويستخدم Debt::latest()
// ============================================================

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index()
    {
        $query = Debt::query();

        // ✅ إذا كان المستخدم ليس مدير، اعرض فقط ديون فرعه
        if (!auth()->user()->isAdmin()) {
            \App\Support\BranchFilter::apply($query);
        }

        // ✅ استخدام $query بدلاً من Debt::latest()
        $debts = $query->with('payments')->latest()->paginate(10);

        $totalDebts = $this->calculateTotalDebts();

        return view('debt.index', compact('debts', 'totalDebts'));
    }

    private function calculateTotalDebts()
    {
        $query = Debt::with('payments')->query();

        if (!auth()->user()->isAdmin()) {
            \App\Support\BranchFilter::apply($query);
        }

        // دائن = "لي عنده" = دين لنا (receivable) | مدين = "عليّ له" = دين علينا (payable)
        // بنحسب المتبقي الفعلي (بعد أي دفعات جزئية)، مش المبلغ الأصلي كامل.
        $openDebts = $query->whereNull('payment_date')->get();

        $receivables = $openDebts->where('type', 'دائن')->sum('remaining_amount');
        $payables = $openDebts->where('type', 'مدين')->sum('remaining_amount');

        return $receivables - $payables;
    }

    public function create()
    {
        return view('debt.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'type'          => 'required|in:دائن,مدين',
            'cash_amount'   => 'nullable|numeric|min:0',
            'bank_amount'   => 'nullable|numeric|min:0',
            'reason'        => 'required|string',
            'debt_date'     => 'required|date',
            'payment_date'  => 'nullable|date|after_or_equal:debt_date',
        ]);

        // ✅ إضافة branch_id بعد التحقق
        $validated['branch_id'] = auth()->user()->branch_id;

        Debt::create($validated);

        return redirect()->route('debts.index')->with('success', 'تم إضافة السجل بنجاح');
    }

    public function show(Debt $debt)
    {
        $debt->load('payments', 'wholesaleInvoice');

        return view('debt.show', compact('debt'));
    }

    public function edit(Debt $debt)
    {
        // الدين المرتبط بفاتورة جملة، مبلغه ومصدره محسوبين من الفاتورة نفسها —
        // تعديله يدوياً هون بيكسر التزامن بينهم.
        abort_if($debt->wholesaleInvoice()->exists(), 403, 'هذا الدين مرتبط بفاتورة بيع بالجملة — عدّله من صفحة الفاتورة نفسها.');

        return view('debt.edit', compact('debt'));
    }

    public function update(Request $request, Debt $debt)
    {
        abort_if($debt->wholesaleInvoice()->exists(), 403, 'هذا الدين مرتبط بفاتورة بيع بالجملة — عدّله من صفحة الفاتورة نفسها.');

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'type'          => 'required|in:دائن,مدين',
            'cash_amount'   => 'nullable|numeric|min:0',
            'bank_amount'   => 'nullable|numeric|min:0',
            'reason'        => 'required|string',
            'debt_date'     => 'required|date',
            'payment_date'  => 'nullable|date|after_or_equal:debt_date',
        ]);

        $debt->load('payments');
        $newTotal = (float) ($validated['cash_amount'] ?? 0) + (float) ($validated['bank_amount'] ?? 0);
        if ($newTotal < $debt->paid_amount - 0.01) {
            return back()
                ->withErrors(['cash_amount' => 'المبلغ الجديد أقل من اللي انسدد فعلاً (' . number_format($debt->paid_amount, 2) . ' شيكل) — عدّل الدفعات المسجّلة بدل ما تنقص المبلغ الأصلي.'])
                ->withInput();
        }

        $debt->update($validated);
        $debt->syncPaymentStatus();

        return redirect()->route('debts.index')->with('success', 'تم تحديث السجل بنجاح');
    }

    public function destroy(Debt $debt)
    {
        abort_if($debt->wholesaleInvoice()->exists(), 403, 'هذا الدين مرتبط بفاتورة بيع بالجملة — احذف الفاتورة نفسها لو بدك تشيل الدين.');

        $debt->delete();
        return redirect()->route('debts.index')->with('success', 'تم حذف السجل بنجاح');
    }
}
