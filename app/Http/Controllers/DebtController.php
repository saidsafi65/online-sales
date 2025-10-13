<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index()
    {
        $debts = Debt::latest()->paginate(10);
        
        // حساب الديون المتراكمة
        $totalDebts = $this->calculateTotalDebts();

        return view('debt.index', compact('debts', 'totalDebts'));
    }

    /**
     * حساب إجمالي الديون المتراكمة
     */
    private function calculateTotalDebts()
    {
        // الديون المستحقة (مدين) = المبالغ التي يجب أن تُدفع لك
        $receivables = Debt::where('type', 'مدين')
            ->whereNull('payment_date')
            ->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        // الديون المستحقة الدفع (دائن) = المبالغ التي يجب أن تدفعها
        $payables = Debt::where('type', 'دائن')
            ->whereNull('payment_date')
            ->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        // صافي الديون = المستحقات - المستحق دفعها
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
            'phone' => 'required|string|max:20',
            'type' => 'required|in:دائن,مدين',
            'cash_amount' => 'nullable|numeric|min:0',
            'bank_amount' => 'nullable|numeric|min:0',
            'reason' => 'required|string',
            'debt_date' => 'required|date',
            'payment_date' => 'nullable|date|after_or_equal:debt_date',
        ]);

        Debt::create($validated);

        return redirect()->route('debts.index')
            ->with('success', 'تم إضافة السجل بنجاح');
    }

    public function edit(Debt $debt)
    {
        return view('debt.edit', compact('debt'));
    }

    public function update(Request $request, Debt $debt)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'type' => 'required|in:دائن,مدين',
            'cash_amount' => 'nullable|numeric|min:0',
            'bank_amount' => 'nullable|numeric|min:0',
            'reason' => 'required|string',
            'debt_date' => 'required|date',
            'payment_date' => 'nullable|date|after_or_equal:debt_date',
        ]);

        $debt->update($validated);

        return redirect()->route('debts.index')
            ->with('success', 'تم تحديث السجل بنجاح');
    }

    public function destroy(Debt $debt)
    {
        $debt->delete();

        return redirect()->route('debts.index')
            ->with('success', 'تم حذف السجل بنجاح');
    }
}