<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
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
use App\Models\Debt;
use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use App\Models\Obligation;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Http\Controllers\MaintenancePartController;
use App\Models\MaintenancePart;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\BranchManagementController;
use App\Http\Controllers\Products\SaleLaptopController;
use App\Http\Controllers\Products\SoftwareController;

// Route::get('/', function () {
//     return view('home');
// })->name('dashboard');

// Auth Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    
    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Logout (يجب أن يكون محمي)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Products (public - available to guests)
Route::get('/products', function () {

    if (auth()->check()) {
        return redirect()->route('products.index-admin');
    }

    // ✅ تم إضافة request() هنا
    return app(\App\Http\Controllers\Products\ProductController::class)->index(request());
})->name('products.index');

// Laptops (public - available to guests)
Route::get('/laptops', function () {

    if (auth()->check()) {
        return redirect()->route('laptops.index-admin');
    }

    return app(\App\Http\Controllers\Products\SaleLaptopController::class)->index(request());
})->name('laptops.index');

// Software (public - available to guests)
Route::get('/software', function () {

    if (auth()->check()) {
        return redirect()->route('software.index-admin');
    }

    return app(\App\Http\Controllers\Products\SoftwareController::class)->index(request());
})->name('software.index');

// الصفحات الثابتة (الدعم الفني / سياسة الخصوصية / شروط الاستخدام)
Route::get('/support', function () {
    return view('legal.support');
})->name('legal.support');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/terms', function () {
    return view('legal.terms');
})->name('legal.terms');

// Root: guests see the products list, authenticated users go to dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // ✅ تم إضافة request() هنا
    return app(\App\Http\Controllers\Products\ProductController::class)->index(request());
})->name('home');


// All management routes (dashboard, sales, repairs, etc.) require authentication
Route::middleware('auth')->group(function () {

    // User and Branch Management (only for admins)
    Route::resource('users', UserManagementController::class);
    Route::resource('branches', BranchManagementController::class);

      // 🔐 إدارة المستخدمين (فقط للمدير)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    // 🏢 إدارة الفروع (فقط للمدير)
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [BranchManagementController::class, 'index'])->name('index');
        Route::get('/create', [BranchManagementController::class, 'create'])->name('create');
        Route::post('/', [BranchManagementController::class, 'store'])->name('store');
        Route::get('/{branch}/edit', [BranchManagementController::class, 'edit'])->name('edit');
        Route::put('/{branch}', [BranchManagementController::class, 'update'])->name('update');
        Route::delete('/{branch}', [BranchManagementController::class, 'destroy'])->name('destroy');
    });

    // Dashboard
    Route::get('/dashboard', function () {

        
        // الحصول على فرع المستخدم إذا لم يكن مدير نظام
         $user = auth()->user();
        $branchFilter = $user->isAdmin() ? null : $user->branch_id;

        
        // ===== إحصائيات المبيعات =====
        $salesQuery = Sale::where('is_returned', false);
        if ($branchFilter) {
            $salesQuery->where('branch_id', $branchFilter);
        }
        
        $monthlySales = $salesQuery
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum(DB::raw('cash_amount + app_amount'));

        $todaySalesCount = $salesQuery
            ->whereDate('created_at', today())
            ->count();

        // ===== إحصائيات الصيانة =====
        $repairsQuery = Repair::where('is_returned', false);
        if ($branchFilter) {
            $repairsQuery->where('branch_id', $branchFilter);
        }

        $deliveredRepairs = $repairsQuery
            ->whereNotNull('delivery_date')
            ->whereMonth('delivery_date', now()->month)
            ->count();

        $monthlycost_cashRepair = $repairsQuery
            ->whereMonth('delivery_date', now()->month)
            ->sum('cost_cash');

        $monthlycost_bankRepair = $repairsQuery
            ->whereMonth('delivery_date', now()->month)
            ->sum('cost_bank');

        $monthlycostRepair = $monthlycost_cashRepair + $monthlycost_bankRepair;

        $pendingRepairs = Repair::query();
        if ($branchFilter) {
            $pendingRepairs->where('branch_id', $branchFilter);
        }
        $pendingRepairs = max($pendingRepairs->where('status', 'pending')->count() - $deliveredRepairs, 0);

        // ===== إحصائيات المشتريات =====
        $purchasesQuery = Purchase::where('is_returned', false);
        if ($branchFilter) {
            $purchasesQuery->where('branch_id', $branchFilter);
        }

        $cashPurchases = $purchasesQuery
            ->whereMonth('purchase_date', now()->month)
            ->sum('amount_cash');

        $bankPurchases = $purchasesQuery
            ->whereMonth('purchase_date', now()->month)
            ->sum('amount_bank');

        $monthlyPurchases = $cashPurchases + $bankPurchases;

        // ===== إحصائيات الالتزامات =====
        $obligationsQuery = Obligation::query();
        if ($branchFilter) {
            $obligationsQuery->where('branch_id', $branchFilter);
        }

        $obligationsCash = $obligationsQuery
            ->whereMonth('date', now()->month)
            ->sum('cash_amount');

        $obligationsBank = $obligationsQuery
            ->whereMonth('date', now()->month)
            ->sum('bank_amount');

        $monthlyObligations = $obligationsCash + $obligationsBank;
        $totalMonthlyPurchases = $monthlyPurchases + $monthlyObligations;

        // ===== إحصائيات الديون =====
        $debtsQuery = Debt::query();
        if ($branchFilter) {
            $debtsQuery->where('branch_id', $branchFilter);
        }

        $totalReceivables = $debtsQuery
            ->where('type', 'مدين')
            ->whereNull('payment_date')
            ->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $totalPayables = $debtsQuery
            ->where('type', 'دائن')
            ->whereNull('payment_date')
            ->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $totalDebts = $totalReceivables - $totalPayables;

        // ===== الحسابات النهائية =====
        $monthlyIncome = $monthlySales + $monthlycostRepair;
        $netRevenue = $monthlyIncome - $totalMonthlyPurchases;

        // ===== بيانات إضافية =====
        $catalogQuery = CatalogItem::query();
        if ($branchFilter) {
            $catalogQuery->where('branch_id', $branchFilter);
        }
        $totalProducts = $catalogQuery->count();

        $repairCount = Repair::query();
        if ($branchFilter) {
            $repairCount->where('branch_id', $branchFilter);
        }
        $totalCustomers = $repairCount->count();       

        // عدد الصيانات المسلمة (مع تطبيق فلتر الفرع إذا لم يكن مدير)
        $deliveredRepairsQuery = Repair::whereNotNull('delivery_date');
        if ($branchFilter) {
            $deliveredRepairsQuery->where('branch_id', $branchFilter);
        }
        $deliveredRepairs = $deliveredRepairsQuery->count();

        // إجمالي تكلفة الصيانات لهذا الشهر (غير المرجعة) مع فلتر الفرع
        $monthlycost_cashRepairQuery = Repair::whereMonth('delivery_date', now()->month)
            ->whereYear('delivery_date', now()->year)
            ->where('is_returned', false);
        if ($branchFilter) {
            $monthlycost_cashRepairQuery->where('branch_id', $branchFilter);
        }
        $monthlycost_cashRepair = $monthlycost_cashRepairQuery->sum('cost_cash');

        $monthlycost_bankRepairQuery = Repair::whereMonth('delivery_date', now()->month)
            ->whereYear('delivery_date', now()->year)
            ->where('is_returned', false);
        if ($branchFilter) {
            $monthlycost_bankRepairQuery->where('branch_id', $branchFilter);
        }
        $monthlycost_bankRepair = $monthlycost_bankRepairQuery->sum('cost_bank');

        $monthlycostRepair = $monthlycost_cashRepair + $monthlycost_bankRepair;

        // عدد الصيانات المعلقة (قبل الخصم) مع فلتر الفرع
        $pendingRepairsRawQuery = Repair::where('status', 'pending');
        if ($branchFilter) {
            $pendingRepairsRawQuery->where('branch_id', $branchFilter);
        }
        $pendingRepairsRaw = $pendingRepairsRawQuery->count();

        // ✅ إجمالي المبيعات لهذا الشهر (غير المرجعة) مع فلتر الفرع
        $monthlySalesQuery = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('is_returned', false);
        if ($branchFilter) {
            $monthlySalesQuery->where('branch_id', $branchFilter);
        }
        $monthlySales = $monthlySalesQuery->sum(DB::raw('cash_amount + app_amount'));

        // ✅ إجمالي المشتريات لهذا الشهر (غير المرجعة) مع فلتر الفرع
        $cashTotalQuery = Purchase::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->where('is_returned', false);
        if ($branchFilter) {
            $cashTotalQuery->where('branch_id', $branchFilter);
        }
        $cashTotal = $cashTotalQuery->sum('amount_cash');

        $bankTotalQuery = Purchase::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->where('is_returned', false);
        if ($branchFilter) {
            $bankTotalQuery->where('branch_id', $branchFilter);
        }
        $bankTotal = $bankTotalQuery->sum('amount_bank');

        // إجمالي الالتزامات الشهرية مع فلتر الفرع
        $obligationsCashTotalQuery = Obligation::whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
        if ($branchFilter) {
            $obligationsCashTotalQuery->where('branch_id', $branchFilter);
        }
        $obligationsCashTotal = $obligationsCashTotalQuery->sum('cash_amount');

        $obligationsBankTotalQuery = Obligation::whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
        if ($branchFilter) {
            $obligationsBankTotalQuery->where('branch_id', $branchFilter);
        }
        $obligationsBankTotal = $obligationsBankTotalQuery->sum('bank_amount');

        $monthlyObligations = $obligationsCashTotal + $obligationsBankTotal;
        // جمع المشتريات مع الالتزامات
        $monthlyPurchases = $cashTotal + $bankTotal;

        $totalMonthlyPurchases = $monthlyPurchases + $monthlyObligations;

        // صافي الدخل = (إجمالي المبيعات + إجمالي تكلفة الصيانات) - إجمالي المشتريات
        $netRevenue = ($monthlySales + $monthlycostRepair) - $totalMonthlyPurchases;
        $monthlyIncome = $monthlySales + $monthlycostRepair;

        // حساب الديون المتراكمة مع فلتر الفرع
        $totalReceivablesQuery = Debt::where('type', 'مدين')
            ->whereNull('payment_date');
        if ($branchFilter) {
            $totalReceivablesQuery->where('branch_id', $branchFilter);
        }
        $totalReceivables = $totalReceivablesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $totalPayablesQuery = Debt::where('type', 'دائن')
            ->whereNull('payment_date');
        if ($branchFilter) {
            $totalPayablesQuery->where('branch_id', $branchFilter);
        }
        $totalPayables = $totalPayablesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $totalDebts = $totalReceivables - $totalPayables;

        return view('home', [
            // المبيعات اليوم مع فلتر الفرع
            'todaySales' => $todaySalesCount,
            // صيانات معلقة بعد خصم المسلَّمة (مصدر مرشح بحسب الفرع)
            'pendingRepairs' => max($pendingRepairsRaw - $deliveredRepairs, 0),
            // عدد العملاء والمنتجات مع فلتر الفرع
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
            // الأرقام المالية (مصادر محلية مفلترة للفرع أو جميع الفروع للمسؤول)
            'monthlyRevenue' => $netRevenue,
            'monthlyIncome' => $monthlyIncome,
            'monthlyPurchases' => $monthlyPurchases,
            'totalDebts' => $totalDebts,
            'totalMonthlyPurchases' => $totalMonthlyPurchases,
            // إضافة متغيرات الديون المستحقة
            'totalReceivables' => $totalReceivables,
            'totalPayables' => $totalPayables,
        ]);
    })->name('dashboard');

    // Mobile Shop (معرض الجوال)
    Route::middleware(['auth', 'mobile.shop.only'])->group(function () {
        Route::prefix('mobile-shop')->name('mobile-shop.')->group(function () {
            Route::get('/', [\App\Http\Controllers\MobileShopController::class, 'index'])->name('index');

            // Maintenance
            Route::get('/maintenance', [\App\Http\Controllers\MobileShopController::class, 'maintenanceIndex'])->name('maintenance.index');
            Route::get('/maintenance/create', [\App\Http\Controllers\MobileShopController::class, 'maintenanceCreate'])->name('maintenance.create');
            Route::post('/maintenance', [\App\Http\Controllers\MobileShopController::class, 'maintenanceStore'])->name('maintenance.store');
            Route::get('/maintenance/{maintenance}/edit', [\App\Http\Controllers\MobileShopController::class, 'maintenanceEdit'])->name('maintenance.edit');
            Route::put('/maintenance/{maintenance}', [\App\Http\Controllers\MobileShopController::class, 'maintenanceUpdate'])->name('maintenance.update');
            Route::delete('/maintenance/{maintenance}', [\App\Http\Controllers\MobileShopController::class, 'maintenanceDestroy'])->name('maintenance.destroy');

            // Sales
            Route::get('/sales', [\App\Http\Controllers\MobileShopController::class, 'salesIndex'])->name('sales.index');
            Route::get('/sales/create', [\App\Http\Controllers\MobileShopController::class, 'salesCreate'])->name('sales.create');
            Route::post('/sales', [\App\Http\Controllers\MobileShopController::class, 'salesStore'])->name('sales.store');
            Route::get('/sales/{sale}/edit', [\App\Http\Controllers\MobileShopController::class, 'salesEdit'])->name('sales.edit');
            Route::put('/sales/{sale}', [\App\Http\Controllers\MobileShopController::class, 'salesUpdate'])->name('sales.update');
            Route::delete('/sales/{sale}', [\App\Http\Controllers\MobileShopController::class, 'salesDestroy'])->name('sales.destroy');

            // Inventory
            Route::get('/inventory', [\App\Http\Controllers\MobileShopController::class, 'inventoryIndex'])->name('inventory.index');
            Route::get('/inventory/create', [\App\Http\Controllers\MobileShopController::class, 'inventoryCreate'])->name('inventory.create');
            Route::post('/inventory', [\App\Http\Controllers\MobileShopController::class, 'inventoryStore'])->name('inventory.store');
            Route::get('/inventory/{inventory}/edit', [\App\Http\Controllers\MobileShopController::class, 'inventoryEdit'])->name('inventory.edit');
            Route::put('/inventory/{inventory}', [\App\Http\Controllers\MobileShopController::class, 'inventoryUpdate'])->name('inventory.update');
            Route::delete('/inventory/{inventory}', [\App\Http\Controllers\MobileShopController::class, 'inventoryDestroy'])->name('inventory.destroy');

            // Debts
            Route::get('/debts', [\App\Http\Controllers\MobileShopController::class, 'debtsIndex'])->name('debts.index');
            Route::get('/debts/create', [\App\Http\Controllers\MobileShopController::class, 'debtsCreate'])->name('debts.create');
            Route::post('/debts', [\App\Http\Controllers\MobileShopController::class, 'debtsStore'])->name('debts.store');
            Route::get('/debts/{debt}/edit', [\App\Http\Controllers\MobileShopController::class, 'debtsEdit'])->name('debts.edit');
            Route::put('/debts/{debt}', [\App\Http\Controllers\MobileShopController::class, 'debtsUpdate'])->name('debts.update');
            Route::delete('/debts/{debt}', [\App\Http\Controllers\MobileShopController::class, 'debtsDestroy'])->name('debts.destroy');

            // Expenses
            Route::get('/expenses', [\App\Http\Controllers\MobileShopController::class, 'expensesIndex'])->name('expenses.index');
            Route::get('/expenses/create', [\App\Http\Controllers\MobileShopController::class, 'expensesCreate'])->name('expenses.create');
            Route::post('/expenses', [\App\Http\Controllers\MobileShopController::class, 'expensesStore'])->name('expenses.store');
            Route::get('/expenses/{expense}/edit', [\App\Http\Controllers\MobileShopController::class, 'expensesEdit'])->name('expenses.edit');
            Route::put('/expenses/{expense}', [\App\Http\Controllers\MobileShopController::class, 'expensesUpdate'])->name('expenses.update');
            Route::delete('/expenses/{expense}', [\App\Http\Controllers\MobileShopController::class, 'expensesDestroy'])->name('expenses.destroy');
        });
    });
    // الصفحة الرئيسية (مبيعات اليوم)
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');

    // صفحة إضافة عملية بيع جديدة
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    // حذف عملية بيع
    Route::delete('/sales/{sale}', [SalesController::class, 'destroy']);

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

    // ==========================================================
    // 🔍 صفحة تشخيص مؤقتة لمشكلة زر الإرجاع - احذفها بعد ما تنحل المشكلة
    // افتحها من المتصفح مباشرة: /debug-return/{id} (حط رقم مبيعة حقيقي)
    // ==========================================================
    Route::get('/debug-return/{id}', function ($id) {
        header('Content-Type: text/html; charset=utf-8');
        $out = "<div dir='rtl' style='font-family: monospace; font-size: 15px; line-height: 1.8; padding: 20px;'>";

        // 1) هل الراوت مسجل أصلاً؟
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('sales.return');
        $out .= "<h3>1) هل route اسمه sales.return مسجل؟</h3>";
        if ($route) {
            $out .= "✅ نعم موجود<br>";
            $out .= "URI: <b>".$route->uri()."</b><br>";
            $out .= "Action: <b>".$route->getActionName()."</b><br>";
        } else {
            $out .= "❌ غير موجود إطلاقًا! (مشكلة route cache قديم — روح على /fix-config)";
        }

        // 2) شو توقيع (signature) دالة returnSale الفعلية المحمّلة حاليًا؟
        $out .= "<h3>2) توقيع دالة returnSale المحمّلة فعليًا</h3>";
        try {
            $ref = new \ReflectionMethod(\App\Http\Controllers\SalesController::class, 'returnSale');
            $params = [];
            foreach ($ref->getParameters() as $p) {
                $type = $p->getType() ? $p->getType()->getName() : 'mixed';
                $params[] = $type.' $'.$p->getName();
            }
            $out .= "returnSale(".implode(', ', $params).")<br>";
            if (str_contains(implode(',', $params), 'Sale $')) {
                $out .= "⚠️ هاد التوقيع القديم (implicit model binding) — يعني SalesController.php الجديد ما انرفع!";
            } else {
                $out .= "✅ هاد التوقيع الجديد (int \$saleId)";
            }
        } catch (\Throwable $e) {
            $out .= "❌ خطأ بقراءة الدالة: ".$e->getMessage();
        }

        // 3) هل bootstrap/app.php الجديد فعليًا مطبق؟ (تحقق غير مباشر عبر رفع استثناء تجريبي)
        $out .= "<h3>3) معلومات المستخدم الحالي</h3>";
        if (auth()->check()) {
            $user = auth()->user();
            $out .= "user_id: ".$user->id."<br>";
            $out .= "role: ".$user->role."<br>";
            $out .= "isAdmin(): ".(method_exists($user, 'isAdmin') && $user->isAdmin() ? 'true' : 'false')."<br>";
            $out .= "branch_id: ".($user->branch_id ?? 'NULL')."<br>";
        } else {
            $out .= "❌ مش مسجل دخول أصلاً بهاي الجلسة";
        }

        // 4) جرب فعليًا تلاقي المبيعة (بدون فلتر الفرع) وشوف قيمها
        $out .= "<h3>4) بيانات المبيعة رقم $id</h3>";
        $sale = \App\Models\Sale::withoutGlobalScope(\App\Models\Scopes\BranchScope::class)->find($id);
        if ($sale) {
            $out .= "✅ موجودة<br>";
            $out .= "branch_id: ".($sale->branch_id ?? 'NULL')."<br>";
            $out .= "is_returned: ".($sale->is_returned ? 'true' : 'false')."<br>";
            $out .= "product: ".$sale->product."<br>";
            $out .= "type: ".$sale->type."<br>";
        } else {
            $out .= "❌ ما في مبيعة بهاد الرقم إطلاقًا بقاعدة البيانات";
        }

        // 5) شغّل نفس منطق returnSale فعليًا وشوف شو بيصير بالضبط (بدون AJAX، بدون JS)
        $out .= "<h3>5) تشغيل فعلي لمنطق الإرجاع</h3>";
        if (request('confirm') !== '1') {
            $out .= "⚠️ هاد الجزء رح <b>ينفذ عملية إرجاع حقيقية</b> على هاي المبيعة (يزيد الكمية فعليًا) إذا ما كانت مرجعة قبل هيك.<br>";
            $out .= "إذا متأكد بدك تجربها فعليًا، افتح نفس الرابط وزود بآخره: <code>?confirm=1</code>";
        } else {
            try {
                $controller = new \App\Http\Controllers\SalesController();
                $response = $controller->returnSale((int) $id);
                $out .= "<b>النتيجة:</b><br><pre>".htmlspecialchars(json_encode(
                    json_decode($response->getContent()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                ))."</pre>";
            } catch (\Throwable $e) {
                $out .= "❌ <b>صار استثناء فعلي:</b><br>";
                $out .= "النوع: ".get_class($e)."<br>";
                $out .= "الرسالة: ".$e->getMessage()."<br>";
                $out .= "الملف: ".$e->getFile().":".$e->getLine()."<br>";
                $out .= "<pre>".htmlspecialchars($e->getTraceAsString())."</pre>";
            }
        }

        $out .= "</div>";
        return $out;
    });

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
    Route::get('/invoices/{id}/receipt', [InvoiceController::class, 'receipt'])->name('invoices.receipt');

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

    // Backup routes
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::get('/create', [BackupController::class, 'create'])->name('create');
        Route::post('/store', [BackupController::class, 'store'])->name('store');
        Route::get('/upload', [BackupController::class, 'upload'])->name('upload');
        Route::post('/upload', [BackupController::class, 'storeUpload'])->name('storeUpload');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::post('/restore/{filename}', [BackupController::class, 'restore'])->name('restore');
        Route::delete('/destroy/{filename}', [BackupController::class, 'destroy'])->name('destroy');
    });

    // Maintenance Parts routes
    Route::prefix('maintenance_parts')->name('maintenance_parts.')->group(function () {
        Route::get('/', [MaintenancePartController::class, 'index'])->name('index');
        Route::get('/create', [MaintenancePartController::class, 'create'])->name('create');
        Route::post('/', [MaintenancePartController::class, 'store'])->name('store');
        Route::get('/{maintenancePart}', [MaintenancePartController::class, 'show'])->name('show');
        Route::get('/{maintenancePart}/edit', [MaintenancePartController::class, 'edit'])->name('edit');
        Route::put('/{maintenancePart}', [MaintenancePartController::class, 'update'])->name('update');
        Route::delete('/{maintenancePart}', [MaintenancePartController::class, 'destroy'])->name('destroy');
    });

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/admin', [ProductController::class, 'index_admin'])->name('index-admin'); // ← اسم ومسار مختلف
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
});

// ===== Laptops Management (Admin Only) =====
Route::get('/laptops-admin', [SaleLaptopController::class, 'index_admin'])->name('laptops.index-admin');
Route::get('/laptops/create', [SaleLaptopController::class, 'create'])->name('laptops.create');
Route::post('/laptops', [SaleLaptopController::class, 'store'])->name('laptops.store');
Route::get('/laptops/{laptop}/edit', [SaleLaptopController::class, 'edit'])->name('laptops.edit');
Route::put('/laptops/{laptop}', [SaleLaptopController::class, 'update'])->name('laptops.update');
Route::delete('/laptops/{laptop}', [SaleLaptopController::class, 'destroy'])->name('laptops.destroy');
Route::delete('/laptop-images/{image}', [SaleLaptopController::class, 'destroyImage'])->name('laptops.images.destroy');


// ===== Software Management (Admin Only) =====
Route::get('/software-admin', [SoftwareController::class, 'index_admin'])->name('software.index-admin');
Route::get('/software/create', [SoftwareController::class, 'create'])->name('software.create');
Route::post('/software', [SoftwareController::class, 'store'])->name('software.store');
Route::get('/software/{software}/edit', [SoftwareController::class, 'edit'])->name('software.edit');
Route::put('/software/{software}', [SoftwareController::class, 'update'])->name('software.update');
Route::delete('/software/{software}', [SoftwareController::class, 'destroy'])->name('software.destroy');


    // Commands Routes
Route::get('/run-migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = Artisan::output();
    Artisan::call('storage:link');
    $linkOutput = Artisan::output();
    return '' . e($migrateOutput) . "\n---\n" . e($linkOutput) . '';
});
    // Clear config cache route
    Route::get('/fix-config', function () {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');

        return '✅ تم مسح الكاش بنجاح (config + route + view)';
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
