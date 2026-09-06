<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Obligation;
use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        [$dateStart, $dateEnd, $type] = $this->parseInputs($request);

        $data = $this->buildReportData($dateStart, $dateEnd, $type, auth()->user());

        return view('reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        [$dateStart, $dateEnd, $type] = $this->parseInputs($request);

        $data = $this->buildReportData($dateStart, $dateEnd, $type, auth()->user());

        $pdf = Pdf::loadView('reports.pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download('report-' . $dateStart->format('Y-m-d') . '-to-' . $dateEnd->format('Y-m-d') . '.pdf');
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function parseInputs(Request $request): array
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $type = $request->input('type', 'all');

        $rawFrom = $request->input('start_date');
        $rawTo   = $request->input('end_date');

        $dateStart = $rawFrom ? Carbon::parse($rawFrom)->startOfDay() : now()->startOfMonth();
        $dateEnd   = $rawTo   ? Carbon::parse($rawTo)->endOfDay()   : now()->endOfMonth();

        return [$dateStart, $dateEnd, $type];
    }

    private function buildReportData(Carbon $dateStart, Carbon $dateEnd, string $type, ?User $user): array
    {
        $isAdmin = $user && method_exists($user, 'isAdmin') ? (bool) $user->isAdmin() : false;
        $userBranchId = $user?->branch_id;

        // Initialize outputs
        $monthlySales = $monthlySalesCash = $monthlySalesAppAmount = null;
        $salesByPaymentMethod = [];

        $totalRepairs = $totalCustomers = $monthlycost_cashRepair = $monthlycost_bankRepair = null;
        $repairsByDevice = [];

        $cashTotal = $bankTotal = $monthlyPurchases = null;
        $topSuppliers = [];

        // Per-branch aggregates for admin (optional arrays)
        $salesByBranch = $repairsCostByBranch = $purchasesByBranch = [];

        // SALES
        if (in_array($type, ['all', 'sales'], true)) {
            $salesQuery = Sale::query()
                ->where('is_returned', false)
                ->whereBetween('sale_date', [$dateStart, $dateEnd]);

            if (!$isAdmin && $userBranchId) {
                $salesQuery->where('branch_id', $userBranchId);
            }

            // Sums
            $monthlySalesCash = (float) $salesQuery->clone()->sum('cash_amount');
            $monthlySalesAppAmount = (float) $salesQuery->clone()->sum('app_amount');
            $monthlySales = $monthlySalesCash + $monthlySalesAppAmount;

            // تفصيل حسب طريقة الدفع الكاملة (نقدي/بطاقة/تطبيق/مختلط)
            $salesByPaymentMethod = $salesQuery->clone()
                ->selectRaw('payment_method, COUNT(*) AS cnt, COALESCE(SUM(cash_amount),0) + COALESCE(SUM(app_amount),0) AS total_sum')
                ->groupBy('payment_method')
                ->get()
                ->mapWithKeys(fn ($row) => [
                    $row->payment_method => [
                        'count' => (int) $row->cnt,
                        'total' => (float) $row->total_sum,
                    ],
                ])
                ->toArray();

            // Per-branch (only for admin)
            if ($isAdmin) {
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
            // عدّ الزبائن حسب الاسم (مافيش ربط بحساب Customer حقيقي بجدول الصيانة) —
            // أدق من الوضع القديم (كان بس بساوي عدد الصيانات نفسه)، بس ممكن يتكرر
            // نفس الزبون لو انكتب اسمه بشكل مختلف شوي بمرة تانية.
            $totalCustomers = (int) $repairsReceivedQuery->clone()->distinct('customer_name')->count('customer_name');

            // تفصيل حسب نوع الجهاز الأكثر صيانة
            $repairsByDevice = $repairsReceivedQuery->clone()
                ->selectRaw('device_name, COUNT(*) AS cnt')
                ->groupBy('device_name')
                ->orderByDesc('cnt')
                ->limit(5)
                ->get()
                ->map(fn ($row) => ['device' => $row->device_name, 'count' => (int) $row->cnt])
                ->toArray();

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

            // أكثر الموردين تعاملاً بالفترة
            $topSuppliers = $purchasesQuery->clone()
                ->whereNotNull('supplier_name')
                ->where('supplier_name', '!=', '')
                ->selectRaw('supplier_name, COALESCE(SUM(amount_cash),0) + COALESCE(SUM(amount_bank),0) AS total_sum')
                ->groupBy('supplier_name')
                ->orderByDesc('total_sum')
                ->limit(5)
                ->get()
                ->map(fn ($row) => ['supplier' => $row->supplier_name, 'total' => (float) $row->total_sum])
                ->toArray();

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

        // OBLIGATIONS (رواتب، إيجار...) — دايمًا محسوبة (بغض النظر عن $type) لأنها لازمة لصافي الدخل
        $obligationsQuery = Obligation::query()->whereBetween('date', [$dateStart, $dateEnd]);
        if (!$isAdmin && $userBranchId) {
            $obligationsQuery->where('branch_id', $userBranchId);
        }
        $monthlyObligations = (float) $obligationsQuery->clone()->sum('cash_amount')
            + (float) $obligationsQuery->clone()->sum('bank_amount');

        // صافي الدخل الحقيقي = (مبيعات + صيانة) - (مشتريات + التزامات شهرية)
        // ملاحظة: مبيعات معرض الجوال ومشترياته منعكسة أصلاً جوا sales/purchases (مزامنة تلقائية)، فمش محتاجين نضيفها لحالها هون.
        $netIncome = (float) ($monthlySales ?? 0) + (float) ($monthlycostRepair ?? 0)
            - (float) ($monthlyPurchases ?? 0) - $monthlyObligations;

        // أسماء الفروع (بدل ما نعرض #id خام بجداول التفصيل)
        $branchNames = Branch::pluck('name', 'id')->toArray();

        return compact(
            // Inputs back to view
            'dateStart',
            'dateEnd',
            'type',

            // Sales
            'monthlySales',
            'monthlySalesCash',
            'monthlySalesAppAmount',
            'salesByPaymentMethod',

            // Repairs
            'monthlycost_cashRepair',
            'monthlycost_bankRepair',
            'monthlycostRepair',
            'totalRepairs',
            'totalCustomers',
            'repairsByDevice',

            // Purchases
            'cashTotal',
            'bankTotal',
            'monthlyPurchases',
            'topSuppliers',

            // Admin per-branch data
            'salesByBranch',
            'repairsCostByBranch',
            'purchasesByBranch',
            'branchNames',
            // Role flags
            'isAdmin',

            // Obligations + net income
            'monthlyObligations',
            'netIncome'
        );
    }
}
