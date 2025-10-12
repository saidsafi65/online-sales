<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DailyHandoverController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LaptopCompatibilityController;
use App\Http\Controllers\MaintenanceDepositController;
use App\Http\Controllers\ObligationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\RepairsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReturnedGoodController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StoreController;
use App\Models\CatalogItem;
use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('home');
// })->name('dashboard');

Route::get('/', function () {

    // عدد الصيانات المسلمة
    $deliveredRepairs = Repair::whereNotNull('delivery_date')->count();
    // إجمالي تكلفة الصيانات لهذا الشهر (غير المرجعة)
    $monthlycost_cashRepair = Repair::whereMonth('delivery_date', now()->month)
        ->whereYear('delivery_date', now()->year)
        ->where('is_returned', false)
        ->sum('cost_cash');
    $monthlycost_bankRepair = Repair::whereMonth('delivery_date', now()->month)
        ->whereYear('delivery_date', now()->year)
        ->where('is_returned', false)
        ->sum('cost_bank');

    $monthlycostRepair = $monthlycost_cashRepair + $monthlycost_bankRepair;

    // عدد الصيانات المعلقة (قبل الخصم)
    $pendingRepairsRaw = Repair::where('status', 'pending')->count();

    // ✅ إجمالي المبيعات لهذا الشهر (غير المرجعة)
    $monthlySales = Sale::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('is_returned', false)
        ->sum(DB::raw('cash_amount + app_amount'));

    // إجمالي المبيعات النقدية لهذا الشهر (غير المرجعة)
    $monthlySalesCash = Sale::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('is_returned', false)
        ->sum('cash_amount');

    // إجمالي المبيعات البنكية لهذا الشهر (غير المرجعة)
    $monthlySalesAppAmount = Sale::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('is_returned', false)
        ->sum('app_amount');

    // ✅ إجمالي المشتريات لهذا الشهر (غير المرجعة)
    $cashTotal = Purchase::whereMonth('purchase_date', now()->month)
        ->whereYear('purchase_date', now()->year)
        ->where('is_returned', false)
        ->sum('amount_cash');

    $bankTotal = Purchase::whereMonth('purchase_date', now()->month)
        ->whereYear('purchase_date', now()->year)
        ->where('is_returned', false)
        ->sum('amount_bank');

    $monthlyPurchases = $cashTotal + $bankTotal;

    // ✅ صافي الدخل = المبيعات - المشتريات
    $netRevenue = $monthlySales - $monthlyPurchases;

    return view('home', [
        'todaySales' => Sale::whereDate('created_at', today())->count(),
        'pendingRepairs' => Repair::where('status', 'pending')->count(),
        // خصم عدد التسليمات من الصيانات المعلقة
        'pendingRepairs' => max($pendingRepairsRaw - $deliveredRepairs, 0),
        // عدد العملاء = عدد الأسماء الفريدة + عدد الصيانات المسلمة
        'totalCustomers' => Repair::count(),
        'totalProducts' => CatalogItem::count(),
        'monthlyRevenue' => $netRevenue,
    ]);
})->name('dashboard');

Route::get('/settings', function () {
    return view('settings.index');
})->name('settings.index');

Route::get('/reports', function () {
    return view('reports.index');
})->name('reports.index');
// الصفحة الرئيسية (مبيعات اليوم)
Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');

// صفحة إضافة عملية بيع جديدة
Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
// حذف عملية بيع
Route::delete('/sales/{sale}', [SaleController::class, 'destroy']);

// تخزين عملية البيع
Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');

// عرض تفاصيل عملية بيع
Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');

// تعديل عملية بيع
Route::get('/sales/{sale}/edit', [SalesController::class, 'edit'])->name('sales.edit');
Route::put('/sales/{sale}', [SalesController::class, 'update'])->name('sales.update');

// عرض إحصائيات الدخل اليومي
Route::get('/sales/daily-income', [SalesController::class, 'dailyIncome'])->name('sales.dailyIncome');

// عرض إحصائيات الدخل الأسبوعي
Route::get('/sales/weekly-income', [SalesController::class, 'weeklyIncome'])->name('sales.weeklyIncome');

// عرض إحصائيات الدخل الشهري
Route::get('/sales/monthly-income', [SalesController::class, 'monthlyIncome'])->name('sales.monthlyIncome');

// إرجاع عملية بيع
Route::post('/sales/{sale}/return', [SalesController::class, 'returnSale'])->name('sales.return');

// Repairs routes
Route::get('/repairs', [RepairsController::class, 'index'])->name('repairs.index');
Route::get('/repairs/create', [RepairsController::class, 'create'])->name('repairs.create');
Route::post('/repairs', [RepairsController::class, 'store'])->name('repairs.store');
Route::get('/repairs/{repair}/edit', [RepairsController::class, 'edit'])->name('repairs.edit');
Route::put('/repairs/{repair}', [RepairsController::class, 'update'])->name('repairs.update');
Route::delete('/repairs/{repair}', [RepairsController::class, 'destroy'])->name('repairs.destroy');

// Purchases routes
Route::get('/purchases', [PurchasesController::class, 'index'])->name('purchases.index');
Route::get('/purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
Route::post('/purchases', [PurchasesController::class, 'store'])->name('purchases.store');
Route::get('/purchases/{purchase}/edit', [PurchasesController::class, 'edit'])->name('purchases.edit');
Route::put('/purchases/{purchase}', [PurchasesController::class, 'update'])->name('purchases.update');
Route::delete('/purchases/{purchase}', [PurchasesController::class, 'destroy'])->name('purchases.destroy');
Route::get('/purchases/create-catalog', [PurchasesController::class, 'createCatalog'])->name('purchases.create-catalog');
Route::post('/purchases/store-catalog', [PurchasesController::class, 'storeCatalog'])->name('purchases.store-catalog');

// Catalog routes
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/create', [CatalogController::class, 'create'])->name('catalog.create');
Route::post('/catalog', [CatalogController::class, 'store'])->name('catalog.store');
Route::delete('/catalog/{item}', [CatalogController::class, 'destroy'])->name('catalog.destroy');
Route::get('/catalog/{item}/edit', [CatalogController::class, 'edit'])->name('catalog.edit');
Route::put('/catalog/{item}', [CatalogController::class, 'update'])->name('catalog.update');

// Maintenance Deposit routes
Route::get('/deposits', [MaintenanceDepositController::class, 'index'])->name('deposits.index');
Route::get('/deposits/create', [MaintenanceDepositController::class, 'create'])->name('deposits.create');
Route::post('/deposits', [MaintenanceDepositController::class, 'store'])->name('deposits.store');
Route::delete('/deposits/{id}', [MaintenanceDepositController::class, 'destroy'])->name('deposits.destroy');
Route::get('/deposits/{id}/edit', [MaintenanceDepositController::class, 'edit'])->name('deposits.edit');
Route::put('/deposits/{id}', [MaintenanceDepositController::class, 'update'])->name('deposits.update');

// Reports routes
Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

// Obligation routes
Route::get('/obligations', [ObligationController::class, 'index'])->name('obligations.index');
Route::get('/obligations/create', [ObligationController::class, 'create'])->name('obligations.create');
Route::post('/obligations', [ObligationController::class, 'store'])->name('obligations.store');

// Invoices routes
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::get('/invoices/{id}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

// صفحة المتطابقات الرئيسية
Route::get('/compatibility', [LaptopCompatibilityController::class, 'index'])
    ->name('compatibility.index');

// عرض تفاصيل جهاز معين
Route::get('/compatibility/laptop/{id}', [LaptopCompatibilityController::class, 'show'])
    ->name('compatibility.show');

// API للحصول على الأجهزة المتوافقة
Route::post('/compatibility/get-compatible', [LaptopCompatibilityController::class, 'getCompatibleLaptops'])
    ->name('compatibility.get-compatible');
// إدارة الأجهزة
Route::get('/compatibility/manage', [LaptopCompatibilityController::class, 'manageLaptops'])
    ->name('compatibility.manage');

Route::post('/compatibility/laptop', [LaptopCompatibilityController::class, 'storeLaptop'])
    ->name('compatibility.store-laptop');

// ربط قطعة بجهاز
Route::post('/compatibility/attach-part', [LaptopCompatibilityController::class, 'attachPart'])
    ->name('compatibility.attach-part');

// إضافة/حذف توافق
Route::post('/compatibility/add', [LaptopCompatibilityController::class, 'addCompatibility'])
    ->name('compatibility.add');

Route::delete('/compatibility/remove', [LaptopCompatibilityController::class, 'removeCompatibility'])
    ->name('compatibility.remove');

// Customer Orders routes
Route::prefix('customer-orders')->name('customer-orders.')->group(function () {
    Route::get('/', [CustomerOrderController::class, 'index'])->name('index');
    Route::get('/create', [CustomerOrderController::class, 'create'])->name('create');
    Route::post('/', [CustomerOrderController::class, 'store'])->name('store');
    Route::get('/{customerOrder}', [CustomerOrderController::class, 'show'])->name('show');
    Route::get('/{customerOrder}/edit', [CustomerOrderController::class, 'edit'])->name('edit');
    Route::put('/{customerOrder}', [CustomerOrderController::class, 'update'])->name('update');
    Route::delete('/{customerOrder}', [CustomerOrderController::class, 'destroy'])->name('destroy');
});

// Daily Handovers routes
Route::prefix('daily-handovers')->name('daily-handovers.')->group(function () {
    Route::get('/', [DailyHandoverController::class, 'index'])->name('index');
    Route::get('/create', [DailyHandoverController::class, 'create'])->name('create');
    Route::post('/', [DailyHandoverController::class, 'store'])->name('store');
    Route::get('/{dailyHandover}/edit', [DailyHandoverController::class, 'edit'])->name('edit');
    Route::put('/{dailyHandover}', [DailyHandoverController::class, 'update'])->name('update');
    Route::delete('/{dailyHandover}', [DailyHandoverController::class, 'destroy'])->name('destroy');
    Route::get('/reports', [DailyHandoverController::class, 'reports'])->name('reports');
});

Route::prefix('returned-goods')->name('returned-goods.')->group(function () {
    Route::get('/', [ReturnedGoodController::class, 'index'])->name('index');
    Route::get('/create', [ReturnedGoodController::class, 'create'])->name('create');
    Route::post('/', [ReturnedGoodController::class, 'store'])->name('store');
    Route::get('/{returnedGood}', [ReturnedGoodController::class, 'show'])->name('show');
    Route::get('/{returnedGood}/edit', [ReturnedGoodController::class, 'edit'])->name('edit');
    Route::put('/{returnedGood}', [ReturnedGoodController::class, 'update'])->name('update');
    Route::delete('/{returnedGood}', [ReturnedGoodController::class, 'destroy'])->name('destroy');
});

Route::prefix('store')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('store.index');
    Route::get('/create', [StoreController::class, 'create'])->name('store.create');
    Route::post('/', [StoreController::class, 'store'])->name('store.store');
    Route::get('/{id}/edit', [StoreController::class, 'edit'])->name('store.edit');
    Route::put('/{id}', [StoreController::class, 'update'])->name('store.update');
    Route::delete('/{id}', [StoreController::class, 'destroy'])->name('store.destroy');
});
// Debts routes
Route::prefix('debts')->name('debts.')->group(function () {
    Route::get('/', [DebtController::class, 'index'])->name('index');    // عرض جميع الديون
    Route::get('/create', [DebtController::class, 'create'])->name('create');  // عرض نموذج إضافة دين جديد
    Route::post('/', [DebtController::class, 'store'])->name('store');   // حفظ دين جديد
    Route::get('{debt}/edit', [DebtController::class, 'edit'])->name('edit'); // عرض نموذج تعديل دين
    Route::put('{debt}', [DebtController::class, 'update'])->name('update'); // تحديث دين
    Route::delete('{debt}', [DebtController::class, 'destroy'])->name('destroy'); // حذف دين
});

// Clear config cache route
Route::get('/fix-config', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');

    return '✅ تم مسح الكاش بنجاح';
});

// Profile routes
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

// Auth routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
