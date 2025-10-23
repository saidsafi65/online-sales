<?php

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

        // إذا كان المستخدم ليس مدير نظام، اعرض فقط تسليمات فرعه
        if (!auth()->user()->isAdmin()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $handovers = DailyHandover::latest('handover_date')
            ->latest('handover_time')
            ->paginate(15);

        // إجمالي الكاش لليوم
        $todayTotalCash = DailyHandover::whereDate('handover_date', today())->sum('cash');
        // إجمالي البنك لليوم
        $todayTotalBank = DailyHandover::whereDate('handover_date', today())->sum('bank');

        // إجمالي الكاش لهذا الشهر
        $monthTotalCash = DailyHandover::whereMonth('handover_date', now()->month)
            ->whereYear('handover_date', now()->year)
            ->sum('cash');

        // إجمالي البنك لهذا الشهر
        $monthTotalBank = DailyHandover::whereMonth('handover_date', now()->month)
            ->whereYear('handover_date', now()->year)
            ->sum('bank');

        return view('daily-handovers.index', compact('handovers', 'todayTotalCash', 'todayTotalBank', 'monthTotalCash', 'monthTotalBank'));
    }

    public function create()
    {
        return view('daily-handovers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'handover_date' => 'required|date',
            'handover_time' => 'required',
            'cash' => 'required|numeric|min:0',
            'bank' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'received_by' => 'nullable|string|max:255',
            'branch_id' => auth()->user()->branch_id, // ✅ أضف الفرع
        ]);

        // إنشاء تسليم جديد مع البيانات المعتمدة
        DailyHandover::create($validated);

        return redirect()->route('daily-handovers.index')
            ->with('success', 'تم إضافة التسليم بنجاح');
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
            'cash' => 'required|numeric|min:0',
            'bank' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'received_by' => 'nullable|string|max:255',
        ]);

        $dailyHandover->update($validated);

        return redirect()->route('daily-handovers.index')
            ->with('success', 'تم تحديث التسليم بنجاح');
    }

    public function destroy(DailyHandover $dailyHandover)
    {
        $dailyHandover->delete();

        return redirect()->route('daily-handovers.index')
            ->with('success', 'تم حذف التسليم بنجاح');
    }

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // إجمالي التسليمات
        $totalHandovers = DailyHandover::whereBetween('handover_date', [$startDate, $endDate])
            ->sum(DB::raw('cash + bank'));

        // إجمالي المبيعات من جدول sales
        $totalSalessalesQuery = DB::table('sales')
            ->whereBetween(DB::raw('DATE(sale_date)'), [$startDate, $endDate])
            ->whereNull('deleted_at');
        if (!auth()->user()->isAdmin()) {
            $totalSalessalesQuery->where('branch_id', auth()->user()->branch_id);
        }
        $totalSalessales = $totalSalessalesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(app_amount, 0)'));

        $totalSalesrepairsQuery = DB::table('repairs')
            ->whereBetween(DB::raw('DATE(delivery_date)'), [$startDate, $endDate]);
        if (!auth()->user()->isAdmin()) {
            $totalSalesrepairsQuery->where('branch_id', auth()->user()->branch_id);
        }
        $totalSalesrepairs = $totalSalesrepairsQuery->sum(DB::raw('COALESCE(cost_cash, 0) + COALESCE(cost_bank, 0)'));

        $totalSales = $totalSalessales + $totalSalesrepairs;
        // الفرق
        $difference = $totalSales - $totalHandovers;

        // تفاصيل يومية
        $dailyData = DailyHandover::whereBetween('handover_date', [$startDate, $endDate])
            ->selectRaw('handover_date, SUM(cash + bank) as daily_handover')
            ->groupBy('handover_date')
            ->orderBy('handover_date', 'desc')
            ->get()
            ->map(function ($handover) {
                // جمع الإيرادات من جدول المبيعات (نقد + تطبيق)
                $salesFromSalesQuery = DB::table('sales')
                    ->whereDate('sale_date', $handover->handover_date)
                    ->whereNull('deleted_at');
                if (!auth()->user()->isAdmin()) {
                    $salesFromSalesQuery->where('branch_id', auth()->user()->branch_id);
                }
                $salesFromSales = $salesFromSalesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(app_amount, 0)'));

                // جمع تكاليف الصيانات التي تم تسليمها في نفس التاريخ
                $salesFromRepairsQuery = DB::table('repairs')
                    ->whereDate('delivery_date', $handover->handover_date);
                if (!auth()->user()->isAdmin()) {
                    $salesFromRepairsQuery->where('branch_id', auth()->user()->branch_id);
                }
                $salesFromRepairs = $salesFromRepairsQuery->sum(DB::raw('COALESCE(cost_cash, 0) + COALESCE(cost_bank, 0)'));

                $sales = $salesFromSales + $salesFromRepairs;

                return [
                    'date' => $handover->handover_date,
                    'handover' => $handover->daily_handover,
                    'sales' => $sales,
                    'difference' => $sales - $handover->daily_handover,
                ];
            });

        return view('daily-handovers.reports', compact(
            'startDate',
            'endDate',
            'totalHandovers',
            'totalSales',
            'difference',
            'dailyData'
        ));

    }
}
