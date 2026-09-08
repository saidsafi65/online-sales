<?php

namespace App\Http\Controllers;

use App\Models\FinancialClaim;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialClaimController extends Controller
{
    public function index()
    {
        $claims = FinancialClaim::orderBy('created_at', 'desc')->paginate(15);

        return view('financial-claims.index', compact('claims'));
    }

    public function create()
    {
        $claimReference = $this->generateReference(now());

        return view('financial-claims.create', compact('claimReference'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|in:ar,en',
            'claim_date' => 'required|date',
            'tor_number' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'organization_name' => 'required|string|max:255',
            'attention_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric',
        ]);

        $total = 0;
        $items = [];
        foreach ($validated['description'] as $i => $description) {
            if ($description === '') {
                continue;
            }
            $amount = (float) $validated['amount'][$i];
            $total += $amount;
            $items[] = [
                'item_number' => count($items) + 1,
                'description' => $description,
                'amount' => $amount,
            ];
        }

        $claimReference = $this->generateReference(Carbon::parse($validated['claim_date']));

        DB::transaction(function () use ($validated, $items, $total, $claimReference) {
            $claim = FinancialClaim::create([
                'claim_reference' => $claimReference,
                'claim_date' => $validated['claim_date'],
                'tor_number' => $validated['tor_number'] ?? null,
                'currency' => $validated['currency'],
                'language' => $validated['language'],
                'organization_name' => $validated['organization_name'],
                'attention_name' => $validated['attention_name'] ?? null,
                'position' => $validated['position'] ?? null,
                'total_amount' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $claim->items()->create($item);
            }
        });

        return redirect()->route('financial-claims.index')
            ->with('success', 'تم إضافة المطالبة المالية بنجاح');
    }

    public function print($id)
    {
        $claim = FinancialClaim::with('items')->findOrFail($id);

        return view('financial-claims.print', compact('claim'));
    }

    public function downloadPdf($id)
    {
        $claim = FinancialClaim::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('financial-claims.pdf', compact('claim'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('financial-claim-'.$claim->claim_reference.'.pdf');
    }

    public function destroy($id)
    {
        $claim = FinancialClaim::findOrFail($id);
        $claim->delete();

        return redirect()->route('financial-claims.index')
            ->with('success', 'تم حذف المطالبة المالية بنجاح');
    }

    /**
     * صيغة "OS-FC-DDMMYY-NN" — NN رقم تسلسلي لنفس اليوم (يبدأ من 01 لكل تاريخ جديد).
     */
    private function generateReference(Carbon $date): string
    {
        $datePart = $date->format('dmy');
        $prefix = "OS-FC-{$datePart}-";

        $countToday = FinancialClaim::where('claim_reference', 'like', $prefix.'%')->count();

        return $prefix.str_pad((string) ($countToday + 1), 2, '0', STR_PAD_LEFT);
    }
}
