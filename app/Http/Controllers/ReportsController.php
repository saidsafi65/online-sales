<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $request = request();
        $date = $request->input('date');  // 'YYYY-MM-DD' أو null
        $type = $request->input('type', 'all');

        // جاهز نحدد التاريخ لو معطى، وإذا لا نأخذ الشهر الحالي
        $dateStart = $date ? \Carbon\Carbon::parse($date)->startOfDay() : now()->startOfMonth();
        $dateEnd = $date ? \Carbon\Carbon::parse($date)->endOfDay() : now()->endOfMonth();

        // المبيعات
        $totalSales = null;
        if ($type == 'all' || $type == 'sales') {
            $query = Sale::where('is_returned', false);
            if ($date) {
                // بحث بيوم محدد
                $query->whereBetween('created_at', [$dateStart, $dateEnd]);
            } else {
                // بحث بالشهر الحالي
                $query->whereBetween('created_at', [$dateStart, $dateEnd]);
            }
            $totalSales = $query->sum(DB::raw('cash_amount + app_amount'));
        }

        // الصيانات
        $totalRepairs = null;
        $totalCustomers = null;
        if ($type == 'all' || $type == 'repairs') {
            $queryRepairs = Repair::query();
            if ($date) {
                $queryRepairs->whereBetween('received_date', [$dateStart, $dateEnd]);
            } else {
                $queryRepairs->whereBetween('received_date', [$dateStart, $dateEnd]);
            }
            $totalRepairs = $queryRepairs->count();
            $totalCustomers = $queryRepairs->count();  // هنا تعتمد على احتساب العملاء بنفس الصيانات
        }

        // المشتريات
        $totalPurchases = null;
        if ($type == 'all' || $type == 'purchases') {
            $queryPurchases = Purchase::where('is_returned', false);
            if ($date) {
                $queryPurchases->whereBetween('purchase_date', [$dateStart, $dateEnd]);
            } else {
                $queryPurchases->whereBetween('purchase_date', [$dateStart, $dateEnd]);
            }
            
            // جمع المبالغ من الأعمدة الجديدة (amount_cash و amount_bank)
            $totalPurchases = $queryPurchases->sum(DB::raw('amount_cash + amount_bank'));
        }


        return view('reports.index', compact('totalSales', 'totalRepairs', 'totalPurchases', 'totalCustomers'));
    }
}
