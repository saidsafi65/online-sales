<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Tenant;
use App\Services\TenantDatabase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlatformReportsController extends Controller
{
    public function index(Request $request)
    {
        $rawFrom = $request->input('date_from');
        $rawTo = $request->input('date_to');
        $dateStart = $rawFrom ? Carbon::parse($rawFrom)->startOfDay() : now()->startOfMonth();
        $dateEnd = $rawTo ? Carbon::parse($rawTo)->endOfDay() : now()->endOfMonth();

        $tenants = Tenant::on('central')->where('is_active', true)->orderBy('name')->get();

        $rows = $tenants->map(function (Tenant $tenant) use ($dateStart, $dateEnd) {
            try {
                $connection = TenantDatabase::connect($tenant, 'platform_report');

                $sales = (float) DB::connection($connection)->table('sales')
                    ->where('is_returned', false)
                    ->whereBetween('sale_date', [$dateStart, $dateEnd])
                    ->sum(DB::raw('COALESCE(cash_amount,0) + COALESCE(app_amount,0)'));

                $repairs = (float) DB::connection($connection)->table('repairs')
                    ->where('is_returned', false)
                    ->whereBetween('delivery_date', [$dateStart, $dateEnd])
                    ->sum(DB::raw('COALESCE(cost_cash,0) + COALESCE(cost_bank,0)'));

                $purchases = (float) DB::connection($connection)->table('purchases')
                    ->where('is_returned', false)
                    ->whereBetween('purchase_date', [$dateStart, $dateEnd])
                    ->sum(DB::raw('COALESCE(amount_cash,0) + COALESCE(amount_bank,0)'));

                $obligations = (float) DB::connection($connection)->table('obligations')
                    ->whereBetween('date', [$dateStart, $dateEnd])
                    ->sum(DB::raw('COALESCE(cash_amount,0) + COALESCE(bank_amount,0)'));

                // طلبات المتجر الإلكتروني — كانت غير محسوبة إطلاقاً بهالتقرير قبل هالتعديل
                $onlineOrders = (float) DB::connection($connection)->table('orders')
                    ->whereIn('status', Order::STATUS_COUNTS_AS_PURCHASED)
                    ->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->sum('total');

                DB::purge($connection);

                return [
                    'tenant' => $tenant,
                    'ok' => true,
                    'sales' => $sales,
                    'repairs' => $repairs,
                    'purchases' => $purchases,
                    'obligations' => $obligations,
                    'online_orders' => $onlineOrders,
                    'net' => $sales + $repairs + $onlineOrders - $purchases - $obligations,
                ];
            } catch (\Throwable $e) {
                return [
                    'tenant' => $tenant,
                    'ok' => false,
                    'sales' => 0, 'repairs' => 0, 'purchases' => 0, 'obligations' => 0, 'online_orders' => 0, 'net' => 0,
                ];
            }
        });

        $totals = [
            'sales' => $rows->sum('sales'),
            'repairs' => $rows->sum('repairs'),
            'purchases' => $rows->sum('purchases'),
            'obligations' => $rows->sum('obligations'),
            'online_orders' => $rows->sum('online_orders'),
            'net' => $rows->sum('net'),
        ];

        return view('system-admin.reports.index', [
            'rows' => $rows,
            'totals' => $totals,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
        ]);
    }
}
