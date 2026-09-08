<?php
// ============================================================
// FILE 1: app/Http/Controllers/DebtController.php
// FIX: index() كان يبني $query بالفلتر ثم يتجاهله ويستخدم Debt::latest()
// ============================================================

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $debts = $query->latest()->paginate(10);

        $totalDebts = $this->calculateTotalDebts();

        return view('debt.index', compact('debts', 'totalDebts'));
    }

    private function calculateTotalDebts()
    {
        $query = Debt::query();

        if (!auth()->user()->isAdmin()) {
            \App\Support\BranchFilter::apply($query);
        }

        // دائن = "لي عنده" = دين لنا (receivable) | مدين = "عليّ له" = دين علينا (payable)
        $receivables = (clone $query)
            ->where('type', 'دائن')
            ->whereNull('payment_date')
            ->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $payables = (clone $query)
            ->where('type', 'مدين')
            ->whereNull('payment_date')
            ->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

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

    public function edit(Debt $debt)
    {
        return view('debt.edit', compact('debt'));
    }

    public function update(Request $request, Debt $debt)
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

        $debt->update($validated);

        return redirect()->route('debts.index')->with('success', 'تم تحديث السجل بنجاح');
    }

    public function destroy(Debt $debt)
    {
        $debt->delete();
        return redirect()->route('debts.index')->with('success', 'تم حذف السجل بنجاح');
    }
}