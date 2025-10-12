<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        $request = request();
        $date = $request->input('date');
        $type = $request->input('type', 'all');

        // تحديد بداية ونهاية الفترة
        $dateStart = $date ? Carbon::parse($date)->startOfDay() : now()->startOfMonth();
        $dateEnd = $date ? Carbon::parse($date)->endOfDay() : now()->endOfMonth();

        // المبيعات
        $monthlySales = $monthlySalesCash = $monthlySalesAppAmount = null;
        if (in_array($type, ['all', 'sales'])) {
            $salesQuery = Sale::where('is_returned', false)
                ->whereBetween('created_at', [$dateStart, $dateEnd]);

            $monthlySalesCash = $salesQuery->sum('cash_amount');
            $monthlySalesAppAmount = $salesQuery->sum('app_amount');
            $monthlySales = $monthlySalesCash + $monthlySalesAppAmount;
        }

        // الصيانات
        $totalRepairs = $totalCustomers = $monthlycost_cashRepair = $monthlycost_bankRepair = null;
        if (in_array($type, ['all', 'repairs'])) {
            $repairsQuery = Repair::whereBetween('received_date', [$dateStart, $dateEnd]);

            $totalRepairs = $repairsQuery->count();
            $totalCustomers = $repairsQuery->count(); // بإمكانك تغييره لاحقًا لحساب العملاء الفعليين

            $purchasesForRepairs = Purchase::where('is_returned', false)
                ->whereBetween('purchase_date', [$dateStart, $dateEnd]);
            $repairsQuery = Repair::where('is_returned', false)
                ->whereBetween('delivery_date', [$dateStart, $dateEnd]);

            $monthlycost_cashRepair = $repairsQuery->sum('cost_cash');
            $monthlycost_bankRepair = $repairsQuery->sum('cost_bank');
            // $monthlycost_cashRepair = $purchasesForRepairs->sum('amount_cash');
            // $monthlycost_bankRepair = $purchasesForRepairs->sum('amount_bank');
        }

        // المشتريات
        $cashTotal = $bankTotal = $monthlyPurchases = null;
        if (in_array($type, ['all', 'purchases'])) {
            $purchasesQuery = Purchase::where('is_returned', false)
                ->whereBetween('purchase_date', [$dateStart, $dateEnd]);

            $cashTotal = $purchasesQuery->sum('amount_cash');
            $bankTotal = $purchasesQuery->sum('amount_bank');
            $monthlyPurchases = $cashTotal + $bankTotal;
        }
        $monthlycostRepair = $monthlycost_cashRepair + $monthlycost_bankRepair;

        return view('reports.index', compact(
            'monthlySales',
            'monthlySalesCash',
            'monthlySalesAppAmount',
            'monthlycost_cashRepair',
            'monthlycost_bankRepair',
            'monthlycostRepair',
            'cashTotal',
            'bankTotal',
            'monthlyPurchases',
            'totalRepairs',
            'totalCustomers'
        ));

    }
}
