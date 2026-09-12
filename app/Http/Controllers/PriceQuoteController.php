<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PriceQuote;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceQuoteController extends Controller
{
    public function index()
    {
        $quotes = PriceQuote::with('items')->orderBy('created_at', 'desc')->paginate(15);

        return view('price-quotes.index', compact('quotes'));
    }

    public function create()
    {
        $quoteNumber = $this->generateReference(now());

        return view('price-quotes.create', compact('quoteNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|in:ar,en',
            'quote_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:quote_date',
            'currency' => 'required|string|max:10',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'price' => 'required|array',
            'price.*' => 'required|numeric',
        ]);

        $total = 0;
        $items = [];
        foreach ($validated['description'] as $i => $description) {
            if ($description === '') {
                continue;
            }
            $quantity = (int) $validated['quantity'][$i];
            $price = (float) $validated['price'][$i];
            $itemTotal = $quantity * $price;
            $total += $itemTotal;

            $items[] = [
                'item_number' => count($items) + 1,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $price,
                'total_price' => $itemTotal,
            ];
        }

        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $afterDiscountAmount = $total - $discountAmount;

        if ($afterDiscountAmount < 0) {
            return back()
                ->withErrors(['discount_amount' => 'مبلغ الخصم أكبر من إجمالي عرض السعر'])
                ->withInput();
        }

        $quoteNumber = $this->generateReference(Carbon::parse($validated['quote_date']));

        DB::transaction(function () use ($validated, $items, $total, $discountAmount, $afterDiscountAmount, $quoteNumber) {
            $quote = PriceQuote::create([
                'quote_number' => $quoteNumber,
                'quote_date' => $validated['quote_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'language' => $validated['language'],
                'currency' => $validated['currency'],
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'] ?? null,
                'client_email' => $validated['client_email'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $total,
                'discount_amount' => $discountAmount,
                'afterDiscount_amount' => $afterDiscountAmount,
            ]);

            foreach ($items as $item) {
                $quote->items()->create($item);
            }
        });

        return redirect()->route('price-quotes.index')
            ->with('success', 'تم إضافة عرض السعر بنجاح');
    }

    public function print($id)
    {
        $quote = PriceQuote::with('items')->findOrFail($id);

        return view('price-quotes.print', compact('quote'));
    }

    public function downloadPdf($id)
    {
        $quote = PriceQuote::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('price-quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('price-quote-'.$quote->quote_number.'.pdf');
    }

    public function destroy($id)
    {
        $quote = PriceQuote::findOrFail($id);
        $quote->delete();

        return redirect()->route('price-quotes.index')
            ->with('success', 'تم حذف عرض السعر بنجاح');
    }

    /**
     * تحويل عرض سعر لفاتورة عادية جاهزة — بدل ما نعيد كتابة نفس البنود يدوياً
     * لما الزبون يوافق على العرض.
     */
    public function convertToInvoice($id)
    {
        $quote = PriceQuote::with('items')->findOrFail($id);

        $invoiceNumber = 'INV-'.date('Ymd').rand(1000, 9999);

        $invoice = DB::transaction(function () use ($quote, $invoiceNumber) {
            $notes = 'محوّلة من عرض سعر رقم '.$quote->quote_number;
            if ($quote->notes) {
                $notes .= "\n".$quote->notes;
            }

            $invoice = Invoice::create([
                'customer_name' => $quote->client_name,
                'invoice_date' => now()->format('Y-m-d'),
                'invoice_number' => $invoiceNumber,
                'notes' => $notes,
                'total_amount' => $quote->total_amount,
                'discount_amount' => $quote->discount_amount,
                'afterDiscount_amount' => $quote->afterDiscount_amount,
            ]);

            foreach ($quote->items as $item) {
                $invoice->items()->create([
                    'item_number' => $item->item_number,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'تم تحويل عرض السعر لفاتورة بنجاح');
    }

    /**
     * صيغة "OS-QT-DDMMYY-NN" — NN رقم تسلسلي لنفس اليوم (يبدأ من 01 لكل تاريخ جديد).
     */
    private function generateReference(Carbon $date): string
    {
        $datePart = $date->format('dmy');
        $prefix = "OS-QT-{$datePart}-";

        $countToday = PriceQuote::where('quote_number', 'like', $prefix.'%')->count();

        return $prefix.str_pad((string) ($countToday + 1), 2, '0', STR_PAD_LEFT);
    }
}
