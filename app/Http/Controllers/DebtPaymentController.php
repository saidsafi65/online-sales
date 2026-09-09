<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Http\Request;

class DebtPaymentController extends Controller
{
    public function store(Request $request, Debt $debt)
    {
        // الدين المرتبط بفاتورة جملة دفعاته بتنسجل من صفحة الفاتورة نفسها فقط،
        // حتى ما يصير مصدرين مختلفين يتعارضوا على نفس الرصيد.
        abort_if($debt->wholesaleInvoice()->exists(), 403, 'هذا الدين مرتبط بفاتورة بيع بالجملة — سجّل الدفعات من صفحة الفاتورة نفسها.');

        $validated = $request->validate([
            'cash_amount' => 'nullable|numeric|min:0',
            'bank_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'payment_date.required' => 'اختر تاريخ الدفعة',
        ]);

        $cash = (float) ($validated['cash_amount'] ?? 0);
        $bank = (float) ($validated['bank_amount'] ?? 0);
        $amount = $cash + $bank;

        if ($amount <= 0) {
            return back()->with('error', 'اكتب مبلغ الدفعة (نقدي أو بنكي)');
        }

        $debt->load('payments');
        if ($amount > $debt->remaining_amount + 0.01) {
            return back()->with('error', 'المبلغ أكبر من المتبقي على الدين (' . number_format($debt->remaining_amount, 2) . ' شيكل)');
        }

        $debt->payments()->create([
            'cash_amount' => $cash,
            'bank_amount' => $bank,
            'payment_date' => $validated['payment_date'],
            'received_by' => auth()->user()->name,
            'notes' => $validated['notes'] ?? null,
        ]);

        $debt->syncPaymentStatus();

        return back()->with('success', 'تم تسجيل الدفعة بنجاح');
    }

    public function destroy(Debt $debt, DebtPayment $payment)
    {
        abort_if($payment->debt_id !== $debt->id, 404);
        abort_if($payment->wholesale_invoice_payment_id !== null, 403, 'هاي الدفعة مسجلة من فاتورة بيع بالجملة — احذفها من صفحة الفاتورة نفسها.');

        $payment->delete();

        $debt->syncPaymentStatus();

        return back()->with('success', 'تم حذف الدفعة');
    }
}
