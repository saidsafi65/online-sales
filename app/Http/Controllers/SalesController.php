<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SalesController extends Controller
{
    /**
     * Display a listing of sales with statistics
     */
    public function index(Request $request): View
    {
        $query = Sale::query();

        // إذا كان المستخدم ليس مدير نظام، اعرض فقط مبيعات فرعه
        if (!auth()->user()->isAdmin()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }
        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            if ($request->status === 'returned') {
                $query->where('is_returned', true);
            } elseif ($request->status === 'completed') {
                $query->where('is_returned', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Get paginated results
        $sales = $query->orderBy('sale_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Calculate statistics
        $statistics = $this->calculateStatistics();

        return view('sales.index', compact('sales', 'statistics'));
    }

    /**
     * Show the form for creating a new sale
     */
    public function create(): View
    {
        // جلب كتالوج المنتجات لتعبئة القوائم (فلترة حسب الفرع للمستخدمين غير الإدمن)
        $catalogQuery = \App\Models\CatalogItem::orderBy('product')->orderBy('type');
        if (! auth()->user()->isAdmin()) {
            $catalogQuery->where('branch_id', auth()->user()->branch_id);
        }
        $catalog = $catalogQuery->get();

        // Group by product, keep only types with quantity > 0, and remove products with no available types
        $products = $catalog->groupBy('product')
            ->map(function ($group) {
                return $group->filter(function ($item) {
                    return isset($item->quantity) && (int) $item->quantity > 0;
                })->pluck('type')->unique()->values();
            })->filter(function ($types) {
                return $types->isNotEmpty();
            });

        return view('sales.create', compact('products'));
    }

    /**
     * Store a newly created sale
     */
    // في دالة store

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'product' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'quantity' => 'nullable|integer|min:1',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,app,mixed',
            'notes' => 'nullable|string|max:1000',
        ];

        // Validate amounts based on payment method
        switch ($request->payment_method) {
            case 'cash':
                $rules['cash_amount'] = 'required|numeric|min:0.01';
                $rules['app_amount'] = 'required|numeric|in:0';
                break;
            case 'card':
            case 'app':
                $rules['app_amount'] = 'required|numeric|min:0.01';
                $rules['cash_amount'] = 'required|numeric|in:0';
                break;
            case 'mixed':
                $rules['cash_amount'] = 'required|numeric|min:0.01';
                $rules['app_amount'] = 'required|numeric|min:0.01';
                break;
            default:
                $rules['cash_amount'] = 'required|numeric|min:0';
                $rules['app_amount'] = 'required|numeric|min:0';
        }

        $validator = Validator::make($request->all(), $rules, [
            'cash_amount.min' => 'المبلغ النقدي يجب أن يكون أكبر من صفر',
            'app_amount.min' => 'مبلغ التطبيق يجب أن يكون أكبر من صفر',
            'cash_amount.in' => 'المبلغ النقدي يجب أن يكون صفر مع طريقة الدفع المختارة',
            'app_amount.in' => 'مبلغ التطبيق يجب أن يكون صفر مع طريقة الدفع المختارة',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->cash_amount == 0 && $request->app_amount == 0) {
            return redirect()->back()
                ->withErrors(['amount' => 'يجب إدخال مبلغ للدفع'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // تحقق من الكمية المتوفرة في جدول catalog_items
            $catalogItem = \App\Models\CatalogItem::where('product', $request->product)
                ->where('type', $request->type)
                ->first();

            // استفاده من الكمية المطلوبة (الافتراضية 1)
            $requestedQuantity = max(1, (int) $request->get('quantity', 1));

            // إذا لم تكن الكمية متوفرة أو أقل من الكمية المطلوبة، ارجع بخطأ
            if (! $catalogItem || $catalogItem->quantity < $requestedQuantity) {
                return redirect()->back()
                    ->withErrors(['quantity' => 'المنتج غير متوفر أو الكمية غير كافية'])
                    ->withInput();
            }

            // تحديث الكمية بعد عملية البيع
            $catalogItem->decrement('quantity', $requestedQuantity);

            // إنشاء عملية البيع
            $sale = Sale::create([
                'product' => $request->product,
                'type' => $request->type,
                'quantity' => $requestedQuantity,
                'sale_date' => $request->sale_date,
                'payment_method' => $request->payment_method,
                'cash_amount' => $request->cash_amount,
                'app_amount' => $request->app_amount,
                'is_returned' => false,
                'branch_id' => auth()->user()->branch_id, // ✅ أضف الفرع
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'تم إضافة المبيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة المبيعة: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified sale
     */
    public function show(Sale $sale): View
    {
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale
     */
    public function edit(Sale $sale): View
    {
        return view('sales.edit', compact('sale'));
    }

    /**
     * Update the specified sale
     */
    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'quantity' => 'nullable|integer|min:1',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,app,mixed',
            'cash_amount' => 'required|numeric|min:0',
            'app_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate that at least one amount is greater than 0
        if ($request->cash_amount == 0 && $request->app_amount == 0) {
            return redirect()->back()
                ->withErrors(['amount' => 'يجب أن يكون إجمالي المبلغ أكبر من صفر'])
                ->withInput();
        }

        try {
            DB::beginTransaction();
            // كمية جديدة وقديمة
            $newQuantity = max(1, (int) $request->get('quantity', $sale->quantity ?? 1));
            $oldQuantity = isset($sale->quantity) ? (int) $sale->quantity : 1;

            $oldProduct = $sale->product;
            $oldType = $sale->type;

            $newProduct = $request->product;
            $newType = $request->type;

            // إذا لم يتغير المنتج/النوع
            if ($oldProduct === $newProduct && $oldType === $newType) {
                $catalogItem = \App\Models\CatalogItem::where('product', $oldProduct)
                    ->where('type', $oldType)
                    ->first();

                if (! $catalogItem) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'العنصر غير موجود في الكاتالوج'])
                        ->withInput();
                }

                $delta = $newQuantity - $oldQuantity; // >0 means need to reduce inventory further

                if ($delta > 0) {
                    if ($catalogItem->quantity < $delta) {
                        return redirect()->back()
                            ->withErrors(['quantity' => 'الكمية المطلوبة أكبر من المتاح في المخزون'])
                            ->withInput();
                    }
                    $catalogItem->decrement('quantity', $delta);
                } elseif ($delta < 0) {
                    // زيادة المخزون بمقدار الفرق
                    $catalogItem->increment('quantity', -$delta);
                }
            } else {
                // استعادة الكمية القديمة إلى الكاتالوج القديم
                $oldCatalog = \App\Models\CatalogItem::where('product', $oldProduct)
                    ->where('type', $oldType)
                    ->first();
                if ($oldCatalog) {
                    $oldCatalog->increment('quantity', $oldQuantity);
                }

                // خصم الكمية الجديدة من الكاتالوج الجديد
                $newCatalog = \App\Models\CatalogItem::where('product', $newProduct)
                    ->where('type', $newType)
                    ->first();

                if (! $newCatalog || $newCatalog->quantity < $newQuantity) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'المنتج الجديد غير متوفر أو الكمية غير كافية'])
                        ->withInput();
                }

                $newCatalog->decrement('quantity', $newQuantity);
            }

            $sale->update([
                'product' => $newProduct,
                'type' => $newType,
                'quantity' => $newQuantity,
                'sale_date' => $request->sale_date,
                'payment_method' => $request->payment_method,
                'cash_amount' => $request->cash_amount,
                'app_amount' => $request->app_amount,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'تم تحديث المبيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المبيعة: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified sale from storage
     */
    public function destroy(Sale $sale): JsonResponse
    {
        try {
            DB::beginTransaction();

            $sale->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المبيعة بنجاح',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المبيعة: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return a sale
     */
    public function returnSale(Sale $sale): JsonResponse
    {
        if ($sale->is_returned) {
            return response()->json([
                'success' => false,
                'message' => 'هذه المبيعة مرتجعة بالفعل',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // تحديث حالة المبيعة
            $sale->update([
                'is_returned' => true,
                'notes' => ($sale->notes ? $sale->notes.' - ' : '').'تم الإرجاع في '.now()->format('Y-m-d H:i:s'),
            ]);

            // البحث عن العنصر المرتبط بالمبيعة
            $catalogItem = \App\Models\CatalogItem::where('product', $sale->product)
                ->where('type', $sale->type)
                ->first();

            if ($catalogItem) {
                // استعادة الكمية بناءً على قيمة 'quantity' في المبيعة (افتراضي 1)
                $restoreQty = isset($sale->quantity) ? (int) $sale->quantity : 1;
                $currentQuantity = (int) $catalogItem->quantity;
                $catalogItem->update([
                    'quantity' => $currentQuantity + $restoreQty,
                ]);
            } else {
                // إذا لم يتم العثور على العنصر، يمكنك إرجاع خطأ أو إنشاء سجل جديد حسب الحاجة
                return response()->json([
                    'success' => false,
                    'message' => 'العنصر غير موجود في الكاتالوج',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع المبيعة بنجاح',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرجاع المبيعة: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sales statistics
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->calculateStatistics();

        return response()->json($statistics);
    }

    /**
     * Export sales data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $query = Sale::query();

        // Apply same filters as index
        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            if ($request->status === 'returned') {
                $query->where('is_returned', true);
            } elseif ($request->status === 'completed') {
                $query->where('is_returned', false);
            }
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        if ($format === 'csv') {
            return $this->exportToCsv($sales);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($sales);
        }

        return redirect()->back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Get daily sales chart data
     */
    public function dailyChart(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);

        $dailySales = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(cash_amount + app_amount) as total')
        )
            ->where('sale_date', '>=', $startDate)
            ->where('is_returned', false)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $counts = [];
        $totals = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('M d');

            $dayData = $dailySales->firstWhere('date', $date);
            $counts[] = $dayData ? $dayData->count : 0;
            $totals[] = $dayData ? (float) $dayData->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'counts' => $counts,
            'totals' => $totals,
        ]);
    }

    /**
     * Get payment method distribution
     */
    public function paymentMethodChart(): JsonResponse
    {
        $paymentMethods = Sale::select('payment_method', DB::raw('COUNT(*) as count'))
            ->where('is_returned', false)
            ->groupBy('payment_method')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'cash' => '#10B981',
            'card' => '#3B82F6',
            'app' => '#8B5CF6',
            'mixed' => '#F59E0B',
        ];

        foreach ($paymentMethods as $method) {
            $labels[] = $this->getPaymentMethodLabel($method->payment_method);
            $data[] = $method->count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values($colors),
        ]);
    }

    /**
     * Calculate comprehensive statistics
     */
    private function calculateStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            // Total statistics
            'totalSales' => Sale::where('is_returned', false)->count(),
            'totalRevenue' => Sale::where('is_returned', false)->sum(DB::raw('cash_amount + app_amount')),
            'returnedSales' => Sale::where('is_returned', true)->count(),

            // Today's statistics
            'todaySales' => Sale::whereDate('sale_date', $today)->where('is_returned', false)->count(),
            'todayRevenue' => Sale::whereDate('sale_date', $today)->where('is_returned', false)->sum(DB::raw('cash_amount + app_amount')),

            // This month's statistics
            'monthSales' => Sale::where('sale_date', '>=', $thisMonth)->where('is_returned', false)->count(),
            'monthRevenue' => Sale::where('sale_date', '>=', $thisMonth)->where('is_returned', false)->sum(DB::raw('cash_amount + app_amount')),

            // Last month's statistics for comparison
            'lastMonthSales' => Sale::whereBetween('sale_date', [$lastMonth, $thisMonth])->where('is_returned', false)->count(),
            'lastMonthRevenue' => Sale::whereBetween('sale_date', [$lastMonth, $thisMonth])->where('is_returned', false)->sum(DB::raw('cash_amount + app_amount')),

            // Payment method breakdown
            'cashSales' => Sale::where('payment_method', 'cash')->where('is_returned', false)->sum('cash_amount'),
            'cardSales' => Sale::where('payment_method', 'card')->where('is_returned', false)->sum(DB::raw('cash_amount + app_amount')),
            'appSales' => Sale::where('payment_method', 'app')->where('is_returned', false)->sum('app_amount'),

            // Average sale amount
            'averageSale' => Sale::where('is_returned', false)->avg(DB::raw('cash_amount + app_amount')),

            // Top products
            'topProducts' => Sale::select('product', DB::raw('COUNT(*) as count'), DB::raw('SUM(cash_amount + app_amount) as total'))
                ->where('is_returned', false)
                ->groupBy('product')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Export sales to CSV
     */
    private function exportToCsv($sales)
    {
        $filename = 'sales_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, [
                'ID',
                'المنتج',
                'النوع',
                'تاريخ البيع',
                'طريقة الدفع',
                'المبلغ النقدي',
                'مبلغ التطبيق',
                'الإجمالي',
                'الحالة',
                'الملاحظات',
            ]);

            // Data
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->id,
                    $sale->product,
                    $sale->type,
                    $sale->formatted_sale_date,
                    $this->getPaymentMethodLabel($sale->payment_method),
                    $sale->cash_amount,
                    $sale->app_amount,
                    $sale->total_amount,
                    $sale->is_returned ? 'مرتجع' : 'مكتمل',
                    $sale->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export sales to Excel (simplified CSV with Excel headers)
     */
    private function exportToExcel($sales)
    {
        // For now, we'll use CSV format with Excel-friendly headers
        // In a real application, you might want to use a library like PhpSpreadsheet
        return $this->exportToCsv($sales);
    }

    /**
     * Get payment method label in Arabic
     */
    private function getPaymentMethodLabel($method): string
    {
        $labels = [
            'cash' => 'نقدي',
            'card' => 'بطاقة',
            'app' => 'تطبيق',
            'mixed' => 'مختلط',
        ];

        return $labels[$method] ?? $method;
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $action = $request->get('action');
        $saleIds = $request->get('sale_ids', []);

        if (empty($saleIds)) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تحديد أي مبيعات',
            ], 400);
        }

        try {
            DB::beginTransaction();

            switch ($action) {
                case 'delete':
                    Sale::whereIn('id', $saleIds)->delete();
                    $message = 'تم حذف المبيعات المحددة بنجاح';
                    break;

                case 'return':
                    Sale::whereIn('id', $saleIds)
                        ->where('is_returned', false)
                        ->update([
                            'is_returned' => true,
                            'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' - تم الإرجاع في ".now()->format('Y-m-d H:i:s')."')"),
                        ]);
                    $message = 'تم إرجاع المبيعات المحددة بنجاح';
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'عملية غير مدعومة',
                    ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية: '.$e->getMessage(),
            ], 500);
        }
    }
}
