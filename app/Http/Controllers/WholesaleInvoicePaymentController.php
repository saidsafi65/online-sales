<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\WholesaleInvoice;
use App\Models\WholesaleInvoicePayment;
use Illuminate\Http\Request;

class WholesaleInvoicePaymentController extends Controller
{
    public function store(Request $request, WholesaleInvoice $wholesaleInvoice)
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

        $wholesaleInvoice->load('payments');
        if ($amount > $wholesaleInvoice->remaining_amount + 0.01) {
            return back()->with('error', 'المبلغ أكبر من المتبقي على الفاتورة (' . number_format($wholesaleInvoice->remaining_amount, 2) . ' شيكل)');
        }

        $payment = WholesaleInvoicePayment::create([
            'wholesale_invoice_id' => $wholesaleInvoice->id,
            'cash_amount' => $cash,
            'bank_amount' => $bank,
            'payment_date' => $validated['payment_date'],
            'received_by' => auth()->user()->name,
            'notes' => $validated['notes'] ?? null,
        ]);

        // لو الفاتورة إلها دين مرتبط (آجل/مختلط)، هاي الدفعة بتقلل منه —
        // منسجلها كدفعة دين مربوطة، حتى صفحة الديون تضل متوافقة تلقائياً
        // بدون ما نلمس رصيد الدين مباشرة.
        if ($wholesaleInvoice->debt_id) {
            $debt = Debt::find($wholesaleInvoice->debt_id);
            if ($debt) {
                $debt->payments()->create([
                    'wholesale_invoice_payment_id' => $payment->id,
                    'cash_amount' => $cash,
                    'bank_amount' => $bank,
                    'payment_date' => $validated['payment_date'],
                    'received_by' => auth()->user()->name,
                    'notes' => 'دفعة من فاتورة الجملة رقم ' . $wholesaleInvoice->invoice_number,
                ]);
                $debt->syncPaymentStatus();
            }
        }

        return back()->with('success', 'تم تسجيل الدفعة بنجاح');
    }

    public function destroy(WholesaleInvoice $wholesaleInvoice, WholesaleInvoicePayment $payment)
    {
        abort_if($payment->wholesale_invoice_id !== $wholesaleInvoice->id, 404);

        $debt = $wholesaleInvoice->debt_id ? Debt::find($wholesaleInvoice->debt_id) : null;

        // حذف الدفعة بيحذف معه دفعة الدين المقابلة تلقائياً (foreign key cascade)
        $payment->delete();

        if ($debt) {
            $debt->syncPaymentStatus();
        }

        return back()->with('success', 'تم حذف الدفعة');
    }
}
