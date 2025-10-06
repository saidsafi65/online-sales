<?php

namespace App\Http\Controllers;

use App\Models\DailyHandover;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyHandoverController extends Controller
{
    public function index()
    {
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
    $totalSales = DB::table('sales')
    ->whereBetween(DB::raw('DATE(sale_date)'), [$startDate, $endDate])
    ->whereNull('deleted_at')
    ->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(app_amount, 0)'));


    // // إجمالي المبيعات (الإيرادات)
    // $totalSales = Sale::whereBetween('sale_date', [$startDate, $endDate])
    //     ->sum('total_price');

    // الفرق
    $difference = $totalSales - $totalHandovers;

    // تفاصيل يومية
    $dailyData = DailyHandover::whereBetween('handover_date', [$startDate, $endDate])
    ->selectRaw('handover_date, SUM(cash + bank) as daily_handover')
    ->groupBy('handover_date')
    ->orderBy('handover_date', 'desc')
    ->get()
    ->map(function ($handover) {
        // استخدم COALESCE مع DB::raw في حال كانت بعض القيم null
        $sales = DB::table('sales')
            ->whereDate('sale_date', $handover->handover_date)
            ->whereNull('deleted_at') // تأكد من عدم وجود صفوف محذوفة
            ->sum(DB::raw('COALESCE(total_price, 0)'));

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