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

        // Inputs: date_from, date_to, type=all|sales|repairs|purchases
        $type = $request->input('type', 'all');

        // Parse date range safely; default to current month
        $rawFrom = $request->input('date_from');
        $rawTo   = $request->input('date_to');

        $dateStart = $rawFrom ? Carbon::parse($rawFrom)->startOfDay() : now()->startOfMonth();
        $dateEnd   = $rawTo   ? Carbon::parse($rawTo)->endOfDay()   : now()->endOfMonth();

        // Safe admin check
        $user = auth()->user();
        $isAdmin = auth()->check() && method_exists($user, 'isAdmin') ? (bool) $user->isAdmin() : false;
        $userBranchId = $user?->branch_id;

        // Initialize outputs
        $monthlySales = $monthlySalesCash = $monthlySalesAppAmount = null;

        $totalRepairs = $totalCustomers = $monthlycost_cashRepair = $monthlycost_bankRepair = null;

        $cashTotal = $bankTotal = $monthlyPurchases = null;

        // Per-branch aggregates for admin (optional arrays)
        $salesByBranch = $repairsCostByBranch = $purchasesByBranch = [];

        // SALES
        if (in_array($type, ['all', 'sales'], true)) {
            $salesQuery = Sale::query()
                ->where('is_returned', false)
                ->whereBetween('created_at', [$dateStart, $dateEnd]);

            if (!$isAdmin && $userBranchId) {
                $salesQuery->where('branch_id', $userBranchId);
            }

            // Sums
            $monthlySalesCash = (float) $salesQuery->clone()->sum('cash_amount');
            $monthlySalesAppAmount = (float) $salesQuery->clone()->sum('app_amount');
            $monthlySales = $monthlySalesCash + $monthlySalesAppAmount;

            // Per-branch (only for admin)
            if ($isAdmin) {
                // Select SUMs per branch; use clone to not disturb base query
                $salesByBranch = $salesQuery->clone()
                    ->selectRaw('branch_id, COALESCE(SUM(cash_amount),0) AS cash_sum, COALESCE(SUM(app_amount),0) AS app_sum')
                    ->groupBy('branch_id')
                    ->get()
                    ->mapWithKeys(function ($row) {
                        $total = (float) $row->cash_sum + (float) $row->app_sum;
                        return [
                            $row->branch_id => [
                                'cash' => (float) $row->cash_sum,
                                'app'  => (float) $row->app_sum,
                                'total'=> $total,
                            ]
                        ];
                    })
                    ->toArray();
            }
        }

        // REPAIRS
        if (in_array($type, ['all', 'repairs'], true)) {
            // Base query to count repairs received within range
            $repairsReceivedQuery = Repair::query()
                ->whereBetween('received_date', [$dateStart, $dateEnd]);

            if (!$isAdmin && $userBranchId) {
                $repairsReceivedQuery->where('branch_id', $userBranchId);
            }

            $totalRepairs = (int) $repairsReceivedQuery->clone()->count();
            // Placeholder: totalCustomers currently equals count; adjust to distinct customers if needed
            $totalCustomers = $totalRepairs;

            // Cost aggregation uses delivery_date and non-returned as in your code
            $repairsDeliveredQuery = Repair::query()
                ->where('is_returned', false)
                ->whereBetween('delivery_date', [$dateStart, $dateEnd]);

            if (!$isAdmin && $userBranchId) {
                $repairsDeliveredQuery->where('branch_id', $userBranchId);
            }

            $monthlycost_cashRepair = (float) $repairsDeliveredQuery->clone()->sum('cost_cash');
            $monthlycost_bankRepair = (float) $repairsDeliveredQuery->clone()->sum('cost_bank');

            // Per-branch for admin
            if ($isAdmin) {
                $repairsCostByBranch = $repairsDeliveredQuery->clone()
                    ->selectRaw('branch_id, COALESCE(SUM(cost_cash),0) AS cash_sum, COALESCE(SUM(cost_bank),0) AS bank_sum')
                    ->groupBy('branch_id')
                    ->get()
                    ->mapWithKeys(function ($row) {
                        $total = (float) $row->cash_sum + (float) $row->bank_sum;
                        return [
                            $row->branch_id => [
                                'cash'  => (float) $row->cash_sum,
                                'bank'  => (float) $row->bank_sum,
                                'total' => $total,
                            ]
                        ];
                    })
                    ->toArray();
            }
        }

        // PURCHASES
        if (in_array($type, ['all', 'purchases'], true)) {
            $purchasesQuery = Purchase::query()
                ->where('is_returned', false)
                ->whereBetween('purchase_date', [$dateStart, $dateEnd]);

            if (!$isAdmin && $userBranchId) {
                $purchasesQuery->where('branch_id', $userBranchId);
            }

            $cashTotal = (float) $purchasesQuery->clone()->sum('amount_cash');
            $bankTotal = (float) $purchasesQuery->clone()->sum('amount_bank');
            $monthlyPurchases = $cashTotal + $bankTotal;

            if ($isAdmin) {
                $purchasesByBranch = $purchasesQuery->clone()
                    ->selectRaw('branch_id, COALESCE(SUM(amount_cash),0) AS cash_sum, COALESCE(SUM(amount_bank),0) AS bank_sum')
                    ->groupBy('branch_id')
                    ->get()
                    ->mapWithKeys(function ($row) {
                        $total = (float) $row->cash_sum + (float) $row->bank_sum;
                        return [
                            $row->branch_id => [
                                'cash'  => (float) $row->cash_sum,
                                'bank'  => (float) $row->bank_sum,
                                'total' => $total,
                            ]
                        ];
                    })
                    ->toArray();
            }
        }

        $monthlycostRepair = null;
        if ($monthlycost_cashRepair !== null || $monthlycost_bankRepair !== null) {
            $monthlycostRepair = (float) ($monthlycost_cashRepair ?? 0) + (float) ($monthlycost_bankRepair ?? 0);
        }

        return view('reports.index', compact(
            // Inputs back to view
            'dateStart',
            'dateEnd',
            'type',

            // Sales
            'monthlySales',
            'monthlySalesCash',
            'monthlySalesAppAmount',

            // Repairs
            'monthlycost_cashRepair',
            'monthlycost_bankRepair',
            'monthlycostRepair',
            'totalRepairs',
            'totalCustomers',

            // Purchases
            'cashTotal',
            'bankTotal',
            'monthlyPurchases',

            // Admin per-branch data
            'salesByBranch',
            'repairsCostByBranch',
            'purchasesByBranch',
            // Role flags
            'isAdmin'
        ));
    }
}