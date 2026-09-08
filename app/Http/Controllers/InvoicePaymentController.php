<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
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

        $invoice->load('payments');
        if ($amount > $invoice->remaining_amount + 0.01) {
            return back()->with('error', 'المبلغ أكبر من المتبقي على الفاتورة (' . number_format($invoice->remaining_amount, 2) . ' شيكل)');
        }

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'cash_amount' => $cash,
            'bank_amount' => $bank,
            'payment_date' => $validated['payment_date'],
            'received_by' => auth()->user()->name,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم تسجيل الدفعة بنجاح');
    }

    public function destroy(Invoice $invoice, InvoicePayment $payment)
    {
        abort_if($payment->invoice_id !== $invoice->id, 404);

        $payment->delete();

        return back()->with('success', 'تم حذف الدفعة');
    }
}
