<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // عرض جميع الفواتير
    public function index()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->paginate(15);
        // تحويل جميع تواريخ الفواتير إلى كائنات Carbon
        foreach ($invoices as $invoice) {
            $invoice->invoice_date = Carbon::parse($invoice->invoice_date);
        }

        return view('invoices.index', compact('invoices'));
    }

    public function receipt($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $invoice->invoice_date = Carbon::parse($invoice->invoice_date);

        return view('invoices.receipt', compact('invoice'));
    }
    // عرض صفحة إضافة فاتورة جديدة
    public function create()
    {
        // إنشاء رقم فاتورة تلقائي
        $invoiceNumber = 'INV-'.date('Ymd').rand(1000, 9999);

        return view('invoices.create', compact('invoiceNumber'));
    }

    // حفظ الفاتورة الجديدة
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
        ]);

        // حساب الإجمالي
        $total = 0;
        $items = [];

        for ($i = 0; $i < count($request->description); $i++) {
            if (! empty($request->description[$i])) {
                $quantity = floatval($request->quantity[$i]);
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

        // حساب مبلغ الخصم والإجمالي بعد الخصم
        $discountAmount = floatval($request->discount_amount ?? 0);
        $afterDiscountAmount = $total - $discountAmount;

        // التأكد من أن المبلغ بعد الخصم لا يكون سالباً
        if ($afterDiscountAmount < 0) {
            return back()
                ->withErrors(['discount_amount' => 'مبلغ الخصم أكبر من إجمالي الفاتورة'])
                ->withInput();
        }

        // إنشاء الفاتورة
        $invoice = Invoice::create([
            'customer_name' => $request->customer_name,
            'invoice_date' => $request->invoice_date,
            'invoice_number' => $request->invoice_number,
            'notes' => $request->notes,
            'total_amount' => $total,
            'discount_amount' => $discountAmount,
            'afterDiscount_amount' => $afterDiscountAmount,
        ]);

        // إضافة العناصر
        foreach ($items as $item) {
            $invoice->items()->create($item);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'تم إضافة الفاتورة بنجاح');
    }

    // عرض الفاتورة
    public function show($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    // طباعة الفاتورة
    public function print($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        // تحويل تاريخ الفاتورة إلى كائن Carbon
        $invoice->invoice_date = Carbon::parse($invoice->invoice_date);

        return view('invoices.print', compact('invoice'));
    }

    // تحميل PDF
    public function downloadPdf($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }

    // حذف الفاتورة
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح');
    }
}
