<?php

namespace App\Http\Controllers;

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
            'payment_terms' => 'required|in:cash,credit',
            'due_date' => 'nullable|date|required_if:payment_terms,credit',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'price' => 'required|array',
            'price.*' => 'required|numeric',
        ], [
            'due_date.required_if' => 'اختر تاريخ الاستحقاق للفاتورة الآجلة',
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
            $invoice = WholesaleInvoice::create([
                'buyer_store_name' => $request->buyer_store_name,
                'buyer_tax_number' => $request->buyer_tax_number,
                'buyer_phone' => $request->buyer_phone,
                'buyer_address' => $request->buyer_address,
                'invoice_date' => $request->invoice_date,
                'invoice_number' => $request->invoice_number,
                'payment_terms' => $request->payment_terms,
                'due_date' => $request->payment_terms === 'credit' ? $request->due_date : null,
                'notes' => $request->notes,
                'total_amount' => $total,
                'discount_amount' => $discountAmount,
                'afterDiscount_amount' => $afterDiscountAmount,
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
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
        $invoice->delete();

        return redirect()->route('wholesale-invoices.index')
            ->with('success', 'تم حذف فاتورة الجملة بنجاح');
    }
}
