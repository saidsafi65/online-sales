<?php
// ============================================================
// FILE: app/Http/Controllers/DailyHandoverController.php
// FIX: branch_id كان داخل validate() - نقله لخارجها
// ============================================================

namespace App\Http\Controllers;

use App\Models\DailyHandover;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyHandoverController extends Controller
{
    public function index()
    {
        $query = DailyHandover::query();

        if (!auth()->user()->isAdmin()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $handovers = $query->latest('handover_date')->latest('handover_time')->paginate(15);

        $todayTotalCash = DailyHandover::whereDate('handover_date', today())->sum('cash');
        $todayTotalBank = DailyHandover::whereDate('handover_date', today())->sum('bank');

        $monthTotalCash = DailyHandover::whereMonth('handover_date', now()->month)
            ->whereYear('handover_date', now()->year)->sum('cash');
        $monthTotalBank = DailyHandover::whereMonth('handover_date', now()->month)
            ->whereYear('handover_date', now()->year)->sum('bank');

        return view('daily-handovers.index', compact(
            'handovers', 'todayTotalCash', 'todayTotalBank', 'monthTotalCash', 'monthTotalBank'
        ));
    }

    public function create()
    {
        return view('daily-handovers.create');
    }

    public function store(Request $request)
    {
        // ✅ branch_id خارج validate
        $validated = $request->validate([
            'handover_date' => 'required|date',
            'handover_time' => 'required',
            'cash'          => 'required|numeric|min:0',
            'bank'          => 'required|numeric|min:0',
            'reason'        => 'required|string|max:255',
            'notes'         => 'nullable|string',
            'received_by'   => 'nullable|string|max:255',
        ]);

        // ✅ إضافة branch_id بعد التحقق
        $validated['branch_id'] = auth()->user()->branch_id;

        DailyHandover::create($validated);

        return redirect()->route('daily-handovers.index')->with('success', 'تم إضافة التسليم بنجاح');
    }

    public function edit(DailyHandover $dailyHandover)
    {
        return view('daily-handovers.edit', compact('dailyHandover'));
    }

    public function update(Request $request, DailyHandover $dailyHandover)
    {
        $validated = $request->validate([
            'handover_date' => 'required|date',
            'handover_time' => 'required',
            'cash'          => 'required|numeric|min:0',
            'bank'          => 'required|numeric|min:0',
            'reason'        => 'required|string|max:255',
            'notes'         => 'nullable|string',
            'received_by'   => 'nullable|string|max:255',
        ]);

        $dailyHandover->update($validated);

        return redirect()->route('daily-handovers.index')->with('success', 'تم تحديث التسليم بنجاح');
    }

    public function destroy(DailyHandover $dailyHandover)
    {
        $dailyHandover->delete();
        return redirect()->route('daily-handovers.index')->with('success', 'تم حذف التسليم بنجاح');
    }

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->format('Y-m-d'));

        $totalHandovers = DailyHandover::whereBetween('handover_date', [$startDate, $endDate])
            ->sum(DB::raw('cash + bank'));

        $totalSalessalesQuery = DB::table('sales')
            ->whereBetween(DB::raw('DATE(sale_date)'), [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->where('is_returned', false);
        if (!auth()->user()->isAdmin()) {
            $totalSalessalesQuery->where('branch_id', auth()->user()->branch_id);
        }
        $totalSalessales = $totalSalessalesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(app_amount, 0)'));

        $totalSalesrepairsQuery = DB::table('repairs')
            ->whereBetween(DB::raw('DATE(delivery_date)'), [$startDate, $endDate])
            ->where('is_returned', false);
        if (!auth()->user()->isAdmin()) {
            $totalSalesrepairsQuery->where('branch_id', auth()->user()->branch_id);
        }
        $totalSalesrepairs = $totalSalesrepairsQuery->sum(DB::raw('COALESCE(cost_cash, 0) + COALESCE(cost_bank, 0)'));

        // المصاريف اللي خرجت فعلياً من نفس الدرج بنفس الفترة (مشتريات + التزامات) — لازم
        // تُطرح قبل ما نقارن بالتسليم الفعلي، وإلا أي مشترى نقدي مشروع بيظهر كأنه "عجز".
        $totalPurchasesQuery = DB::table('purchases')
            ->whereBetween(DB::raw('DATE(purchase_date)'), [$startDate, $endDate])
            ->whereNull('deleted_at');
        if (!auth()->user()->isAdmin()) {
            $totalPurchasesQuery->where('branch_id', auth()->user()->branch_id);
        }
        $totalPurchases = $totalPurchasesQuery->sum(DB::raw('COALESCE(amount_cash, 0) + COALESCE(amount_bank, 0)'));

        $totalObligationsQuery = DB::table('obligations')
            ->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        if (!auth()->user()->isAdmin()) {
            $totalObligationsQuery->where('branch_id', auth()->user()->branch_id);
        }
        $totalObligations = $totalObligationsQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $totalSales    = $totalSalessales + $totalSalesrepairs;
        $totalExpenses = $totalPurchases + $totalObligations;
        $expectedCash  = $totalSales - $totalExpenses;
        $difference    = $expectedCash - $totalHandovers;

        $dailyData = DailyHandover::whereBetween('handover_date', [$startDate, $endDate])
            ->selectRaw('handover_date, SUM(cash + bank) as daily_handover')
            ->groupBy('handover_date')
            ->orderBy('handover_date', 'desc')
            ->get()
            ->map(function ($handover) {
                $salesFromSalesQuery = DB::table('sales')
                    ->whereDate('sale_date', $handover->handover_date)
                    ->whereNull('deleted_at')
                    ->where('is_returned', false);
                if (!auth()->user()->isAdmin()) {
                    $salesFromSalesQuery->where('branch_id', auth()->user()->branch_id);
                }
                $salesFromSales = $salesFromSalesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(app_amount, 0)'));

                $salesFromRepairsQuery = DB::table('repairs')
                    ->whereDate('delivery_date', $handover->handover_date)
                    ->where('is_returned', false);
                if (!auth()->user()->isAdmin()) {
                    $salesFromRepairsQuery->where('branch_id', auth()->user()->branch_id);
                }
                $salesFromRepairs = $salesFromRepairsQuery->sum(DB::raw('COALESCE(cost_cash, 0) + COALESCE(cost_bank, 0)'));

                $sales = $salesFromSales + $salesFromRepairs;

                $purchasesQuery = DB::table('purchases')
                    ->whereDate('purchase_date', $handover->handover_date)
                    ->whereNull('deleted_at');
                if (!auth()->user()->isAdmin()) {
                    $purchasesQuery->where('branch_id', auth()->user()->branch_id);
                }
                $purchases = $purchasesQuery->sum(DB::raw('COALESCE(amount_cash, 0) + COALESCE(amount_bank, 0)'));

                $obligationsQuery = DB::table('obligations')
                    ->whereDate('date', $handover->handover_date);
                if (!auth()->user()->isAdmin()) {
                    $obligationsQuery->where('branch_id', auth()->user()->branch_id);
                }
                $obligations = $obligationsQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

                $expenses = $purchases + $obligations;
                $expected = $sales - $expenses;

                return [
                    'date'       => $handover->handover_date,
                    'handover'   => $handover->daily_handover,
                    'sales'      => $sales,
                    'expenses'   => $expenses,
                    'expected'   => $expected,
                    'difference' => $expected - $handover->daily_handover,
                ];
            });

        return view('daily-handovers.reports', compact(
            'startDate', 'endDate', 'totalHandovers', 'totalSales', 'totalExpenses', 'expectedCash', 'difference', 'dailyData'
        ));
    }
}