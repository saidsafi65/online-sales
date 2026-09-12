<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\WholesaleInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WholesaleInvoiceController extends Controller
{
    public function index()
    {
        $invoices = WholesaleInvoice::with(['items', 'payments'])->orderBy('created_at', 'desc')->paginate(15);

        return view('wholesale-invoices.index', compact('invoices'));
    }

    public function create()
    {
        $invoiceNumber = 'WS-'.date('Ymd').rand(1000, 9999);

        return view('wholesale-invoices.create', compact('invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'buyer_store_name' => 'required|string|max:255',
            'buyer_tax_number' => 'nullable|string|max:100',
            'buyer_phone' => 'nullable|string|max:50',
            'buyer_address' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'invoice_number' => 'required|string|unique:wholesale_invoices,invoice_number',
            'payment_terms' => 'required|in:cash,credit,mixed',
            'due_date' => 'nullable|date|required_if:payment_terms,credit,mixed',
            'cash_paid_now' => 'nullable|numeric|min:0.01|required_if:payment_terms,mixed',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'price' => 'required|array',
            'price.*' => 'required|numeric',
        ], [
            'due_date.required_if' => 'اختر تاريخ الاستحقاق للجزء الآجل من الفاتورة',
            'cash_paid_now.required_if' => 'اكتب المبلغ النقدي اللي انقبض فوراً',
        ]);

        $total = 0;
        $items = [];

        for ($i = 0; $i < count($request->description); $i++) {
            if (! empty($request->description[$i])) {
                $quantity = intval($request->quantity[$i]);
                $price = floatval($request->price[$i]);
                $itemTotal = $quantity * $price;
                $total += $itemTotal;

                $items[] = [
                    'item_number' => $i + 1,
                    'description' => $request->description[$i],
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $itemTotal,
                ];
            }
        }

        $discountAmount = floatval($request->discount_amount ?? 0);
        $afterDiscountAmount = $total - $discountAmount;

        if ($afterDiscountAmount < 0) {
            return back()
                ->withErrors(['discount_amount' => 'مبلغ الخصم أكبر من إجمالي الفاتورة'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $total, $discountAmount, $afterDiscountAmount, $items) {
            $paymentTerms = $request->payment_terms;
            $isCredit = in_array($paymentTerms, ['credit', 'mixed'], true);

            $invoice = WholesaleInvoice::create([
                'buyer_store_name' => $request->buyer_store_name,
                'buyer_tax_number' => $request->buyer_tax_number,
                'buyer_phone' => $request->buyer_phone,
                'buyer_address' => $request->buyer_address,
                'invoice_date' => $request->invoice_date,
                'invoice_number' => $request->invoice_number,
                'payment_terms' => $paymentTerms,
                'due_date' => $isCredit ? $request->due_date : null,
                'cash_paid_now' => $paymentTerms === 'mixed' ? (float) $request->cash_paid_now : null,
                'notes' => $request->notes,
                'total_amount' => $total,
                'discount_amount' => $discountAmount,
                'afterDiscount_amount' => $afterDiscountAmount,
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            // نقدي = المبلغ كامل مقبوض فوراً. جزء نقدي وجزء آجل = بس جزء منه.
            // آجل بالكامل = ولا شيء مقبوض الآن.
            $cashNow = match ($paymentTerms) {
                'cash' => $afterDiscountAmount,
                'mixed' => (float) $request->cash_paid_now,
                default => 0,
            };

            if ($cashNow > 0) {
                $invoice->payments()->create([
                    'cash_amount' => min($cashNow, $afterDiscountAmount),
                    'bank_amount' => 0,
                    'payment_date' => $request->invoice_date,
                    'received_by' => auth()->user()->name,
                    'notes' => $paymentTerms === 'cash'
                        ? 'دفعة نقدية كاملة عند إصدار الفاتورة'
                        : 'دفعة نقدية جزئية عند إصدار الفاتورة',
                ]);
            }

            // الباقي (لو في) بيصير دين على المحل المشتري — يظهر بصفحة الديون كمان
            $remaining = round($afterDiscountAmount - $cashNow, 2);
            if ($isCredit && $remaining > 0.01) {
                $debt = Debt::create([
                    'customer_name' => $request->buyer_store_name,
                    'phone' => $request->buyer_phone ?: 'غير محدد',
                    'type' => 'دائن',
                    'cash_amount' => $remaining,
                    'bank_amount' => 0,
                    'reason' => 'فاتورة بيع بالجملة رقم ' . $invoice->invoice_number,
                    'debt_date' => $request->invoice_date,
                ]);
                $invoice->update(['debt_id' => $debt->id]);
            }
        });

        return redirect()->route('wholesale-invoices.index')
            ->with('success', 'تم إضافة فاتورة الجملة بنجاح');
    }

    public function show($id)
    {
        $invoice = WholesaleInvoice::with(['items', 'payments'])->findOrFail($id);

        return view('wholesale-invoices.show', compact('invoice'));
    }

    public function print($id)
    {
        $invoice = WholesaleInvoice::with('items')->findOrFail($id);

        return view('wholesale-invoices.print', compact('invoice'));
    }

    public function downloadPdf($id)
    {
        $invoice = WholesaleInvoice::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('wholesale-invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('wholesale-invoice-'.$invoice->invoice_number.'.pdf');
    }

    public function destroy($id)
    {
        $invoice = WholesaleInvoice::findOrFail($id);

        if ($invoice->debt_id) {
            Debt::where('id', $invoice->debt_id)->delete();
        }

        $invoice->delete();

        return redirect()->route('wholesale-invoices.index')
            ->with('success', 'تم حذف فاتورة الجملة بنجاح');
    }

    /**
     * تذكير SMS بالمبلغ المتبقي على فاتورة جملة آجلة/مختلطة.
     */
    public function sendReminder($id)
    {
        $invoice = WholesaleInvoice::with('payments')->findOrFail($id);

        if (! $invoice->buyer_phone) {
            return back()->with('error', 'ما في رقم جوال مسجّل لهاد المحل — عدّل الفاتورة وضيفه أول');
        }

        if ($invoice->remaining_amount <= 0) {
            return back()->with('error', 'هاي الفاتورة مسددة بالكامل، ما في داعي لتذكير');
        }

        $storeName = app()->bound('currentTenant') ? app('currentTenant')->name : 'Online Sale';
        $remainingText = $invoice->remaining_amount == floor($invoice->remaining_amount)
            ? number_format($invoice->remaining_amount, 0)
            : number_format($invoice->remaining_amount, 2);

        // ما بنحط رقم الفاتورة بالرسالة قصداً — رقم الفاتورة متغيّر الطول وممكن
        // يدفع الرسالة فوق حد الرسالة الوحدة، وقتها القص التلقائي بيقص اسم
        // المعرض (الجزء الثابت والمهم) بدل ما يقص رقم الفاتورة (الأقل أهمية هون).
        $message = \App\Support\SmsTextBuilder::build(
            fn (string $store) => "تذكير من {$store}: عليكم {$remainingText}₪ لطلبية سابقة. يرجى التسديد.",
            $storeName
        );

        try {
            $sent = app(\App\Services\SmsService::class)->send($invoice->buyer_phone, $message, 'wholesale_invoice_reminder');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Wholesale invoice reminder SMS failed', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            $sent = false;
        }

        return $sent
            ? back()->with('success', 'تم إرسال التذكير (أو تسجيله بالوضع التجريبي)')
            : back()->with('error', 'تعذر إرسال التذكير');
    }
}
