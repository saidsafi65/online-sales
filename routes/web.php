<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DailyHandoverController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FinancialClaimController;
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
use App\Http\Controllers\TenantManagementController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\PlatformSupportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PlatformAuthController;
use App\Http\Controllers\PlatformSetupController;
use App\Http\Controllers\PlatformDashboardController;
use App\Http\Controllers\PlatformAccountController;
use App\Http\Controllers\BrandingController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PlatformReportsController;
use App\Models\CatalogItem;
use App\Models\Debt;
use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use App\Models\Obligation;
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
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\JawwalPayController;
use App\Http\Controllers\OnlineOrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\CustomerPushSubscriptionController;


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

// صفحة تفاصيل منتج واحد (عامة، للزباين/الزوار) — منفصلة عن products.show الإدارية
// (يلي محمية بصلاحية الموظفين تحت section.permission:products، ومحجوزة لهم فقط)
Route::get('/products/view/{product}', [\App\Http\Controllers\Products\ProductController::class, 'show'])->name('products.detail');

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

// PWA manifest (public, must sit at the domain root — tenancy here is domain-based, not path-based)
Route::get('/manifest.json', [ManifestController::class, 'show'])->name('manifest');

// Root: guests see the products list, authenticated users go to dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // ✅ تم إضافة request() هنا
    return app(\App\Http\Controllers\Products\ProductController::class)->index(request());
})->name('home');


// All management routes (dashboard, sales, repairs, etc.) require authentication
Route::middleware(['auth', 'ensure.active'])->group(function () {

    // User and Branch Management (only for admins)
    Route::middleware('section.permission:admin_only')->group(function () {
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

        // 🎨 هوية المعرض (شعار + ألوان) - فقط لمدير المعرض
        Route::prefix('branding')->name('branding.')->group(function () {
            Route::get('/', [BrandingController::class, 'edit'])->name('edit');
            Route::post('/', [BrandingController::class, 'update'])->name('update');
            Route::post('/icon', [BrandingController::class, 'updateIcon'])->name('icon');
        });

        // الاشعارات
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    });

    // إدارة الطلبات الإلكترونية (Online Orders) — صلاحية مستقلة، مش حكرة على الأدمن
    Route::prefix('online-orders')->name('online-orders.')->middleware('section.permission:online_orders')->group(function () {
        Route::get('/', [OnlineOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OnlineOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [OnlineOrderController::class, 'updateStatus'])->name('update-status');
    });

    // 🗨️ مجتمع المعارض (محادثة عامة + محادثات خاصة بين المدراء والموظفين المصرّح لهم) — صلاحية مستقلة، مش حكرة على الأدمن
    Route::prefix('community')->name('community.')->middleware('section.permission:community')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/messages', [ChatController::class, 'publicMessages'])->name('messages');
        Route::post('/messages', [ChatController::class, 'sendPublicMessage'])->name('messages.send')->middleware('throttle:30,1');
        Route::get('/directory', [ChatController::class, 'directory'])->name('directory');
        Route::get('/unread-summary', [ChatController::class, 'unreadSummary'])->name('unread-summary');
        Route::get('/conversations/{member}', [ChatController::class, 'conversation'])->name('conversations.show');
        Route::get('/conversations/{conversation}/messages', [ChatController::class, 'privateMessages'])->name('conversations.messages');
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendPrivateMessage'])->name('conversations.messages.send')->middleware('throttle:30,1');
    });

    // 🎧 الدعم الفني (تذاكر داخلية للموظفين) — متاح لأي موظف نشط بغض النظر عن صلاحياته
    // التفصيلية (نفس منطق /notifications). مسار /support-tickets مقصود، مش /support —
    // هيدا الأخير محجوز أصلاً لصفحة "الدعم الفني" العامة الثابتة (legal.support) للزوار.
    Route::prefix('support-tickets')->name('support.')->group(function () {
        Route::get('/', [SupportTicketController::class, 'index'])->name('index');
        Route::get('/create', [SupportTicketController::class, 'create'])->name('create');
        Route::post('/', [SupportTicketController::class, 'store'])->name('store')->middleware('throttle:10,1');
    });

    // إشعارات الدفع (Web Push) — متاحة لأي موظف نشط، بغض النظر عن صلاحياته التفصيلية
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    // 🔍 البحث السريع الشامل (Ctrl+K) — متاح لأي موظف نشط، النتائج نفسها مفلترة داخلياً
    // حسب canViewSection لكل قسم، فما بيشوف الموظف نتائج بقسم ما إله صلاحية عليه.
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    // Dashboard
    Route::get('/dashboard', function () {

        // الحصول على فرع المستخدم إذا لم يكن مدير نظام
        $user = auth()->user();
        $branchFilter = $user->isAdmin() ? null : $user->branch_id;

        // ===== إحصائيات المبيعات =====
        $todaySalesCountQuery = Sale::where('is_returned', false)
            ->whereDate('created_at', today());
        if ($branchFilter) {
            $todaySalesCountQuery->where('branch_id', $branchFilter);
        }
        $todaySalesCount = $todaySalesCountQuery->count();

        // ✅ إجمالي المبيعات لهذا الشهر فقط (غير المرجعة) مع فلتر الفرع
        // (نفلتر على sale_date، مش created_at، حتى يتطابق هالرقم مع صفحة "التقارير" — الاثنين
        // لازم يستخدموا نفس تاريخ العملية الفعلي، مش تاريخ إدخالها بالنظام)
        $monthlySalesQuery = Sale::where('is_returned', false)
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year);
        if ($branchFilter) {
            $monthlySalesQuery->where('branch_id', $branchFilter);
        }
        $monthlySales = $monthlySalesQuery->sum(DB::raw('cash_amount + app_amount'));

        // ===== إحصائيات الصيانة =====
        // عدد الصيانات المسلَّمة (كل الأوقات — تُستخدم فقط لتصحيح عدّاد "المعلّقة")
        $deliveredRepairsQuery = Repair::whereNotNull('delivery_date');
        if ($branchFilter) {
            $deliveredRepairsQuery->where('branch_id', $branchFilter);
        }
        $deliveredRepairs = $deliveredRepairsQuery->count();

        // إجمالي تكلفة الصيانات لهذا الشهر فقط (غير المرجعة) مع فلتر الفرع
        $monthlycostCashRepairQuery = Repair::where('is_returned', false)
            ->whereMonth('delivery_date', now()->month)
            ->whereYear('delivery_date', now()->year);
        if ($branchFilter) {
            $monthlycostCashRepairQuery->where('branch_id', $branchFilter);
        }
        $monthlycostCashRepair = $monthlycostCashRepairQuery->sum('cost_cash');

        $monthlycostBankRepairQuery = Repair::where('is_returned', false)
            ->whereMonth('delivery_date', now()->month)
            ->whereYear('delivery_date', now()->year);
        if ($branchFilter) {
            $monthlycostBankRepairQuery->where('branch_id', $branchFilter);
        }
        $monthlycostBankRepair = $monthlycostBankRepairQuery->sum('cost_bank');

        $monthlycostRepair = $monthlycostCashRepair + $monthlycostBankRepair;

        // عدد الصيانات المعلقة (قبل الخصم) مع فلتر الفرع — حالة حالية، غير مرتبطة بالشهر
        $pendingRepairsRawQuery = Repair::where('status', 'pending');
        if ($branchFilter) {
            $pendingRepairsRawQuery->where('branch_id', $branchFilter);
        }
        $pendingRepairsRaw = $pendingRepairsRawQuery->count();
        $pendingRepairs = max($pendingRepairsRaw - $deliveredRepairs, 0);

        // ===== إحصائيات المشتريات =====
        // ✅ إجمالي المشتريات لهذا الشهر فقط (غير المرجعة) مع فلتر الفرع
        $cashPurchasesQuery = Purchase::where('is_returned', false)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year);
        if ($branchFilter) {
            $cashPurchasesQuery->where('branch_id', $branchFilter);
        }
        $cashPurchases = $cashPurchasesQuery->sum('amount_cash');

        $bankPurchasesQuery = Purchase::where('is_returned', false)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year);
        if ($branchFilter) {
            $bankPurchasesQuery->where('branch_id', $branchFilter);
        }
        $bankPurchases = $bankPurchasesQuery->sum('amount_bank');

        $monthlyPurchases = $cashPurchases + $bankPurchases;

        // ===== إحصائيات الالتزامات (لهذا الشهر فقط) =====
        $obligationsCashQuery = Obligation::whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
        if ($branchFilter) {
            $obligationsCashQuery->where('branch_id', $branchFilter);
        }
        $obligationsCash = $obligationsCashQuery->sum('cash_amount');

        $obligationsBankQuery = Obligation::whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
        if ($branchFilter) {
            $obligationsBankQuery->where('branch_id', $branchFilter);
        }
        $obligationsBank = $obligationsBankQuery->sum('bank_amount');

        $monthlyObligations = $obligationsCash + $obligationsBank;
        $totalMonthlyPurchases = $monthlyPurchases + $monthlyObligations;

        // طلبات المتجر الإلكتروني لهذا الشهر — الطلبات مش مربوطة بفرع معيّن (المتجر
        // مشترك بين كل الفروع)، فمنضيفها بس لعرض المدير (كل الفروع)، مش لعرض فرع محدد.
        $monthlyOnlineOrders = 0;
        if (! $branchFilter) {
            $monthlyOnlineOrders = \App\Models\Order::whereIn('status', \App\Models\Order::STATUS_COUNTS_AS_PURCHASED)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total');
        }

        // ===== الحسابات النهائية (لهذا الشهر فقط) =====
        $monthlyIncome = $monthlySales + $monthlycostRepair + $monthlyOnlineOrders;
        $netRevenue = $monthlyIncome - $totalMonthlyPurchases;

        // ===== إحصائيات الديون المتراكمة (كل الأوقات، غير المسدَّدة فقط) =====
        // دائن = "لي عنده" = دين لنا (receivable) | مدين = "عليّ له" = دين علينا (payable)
        // أي دين له تاريخ سداد (payment_date) يعتبر مسدَّد ولا يُحسب هون
        $totalReceivablesQuery = Debt::where('type', 'دائن')->whereNull('payment_date');
        if ($branchFilter) {
            $totalReceivablesQuery->where('branch_id', $branchFilter);
        }
        $totalReceivables = $totalReceivablesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $totalPayablesQuery = Debt::where('type', 'مدين')->whereNull('payment_date');
        if ($branchFilter) {
            $totalPayablesQuery->where('branch_id', $branchFilter);
        }
        $totalPayables = $totalPayablesQuery->sum(DB::raw('COALESCE(cash_amount, 0) + COALESCE(bank_amount, 0)'));

        $totalDebts = $totalReceivables - $totalPayables;

        // ===== بيانات إضافية (إجماليات كلية، غير مرتبطة بالشهر) =====
        $catalogQuery = CatalogItem::query();
        if ($branchFilter) {
            $catalogQuery->where('branch_id', $branchFilter);
        }
        $totalProducts = $catalogQuery->count();

        // عملاء الصيانة لهذا الشهر فقط: كل سجل صيانة استُلم هذا الشهر يُحسب كزبون (مع فلتر الفرع)
        $repairCustomersQuery = Repair::whereMonth('received_date', now()->month)
            ->whereYear('received_date', now()->year);
        if ($branchFilter) {
            $repairCustomersQuery->where('branch_id', $branchFilter);
        }
        $repairCustomers = $repairCustomersQuery->count();

        // عملاء المبيعات لهذا الشهر فقط: كل مبيعة غير مرجعة هذا الشهر تُحسب كزبون (مع فلتر الفرع)
        $salesCustomersQuery = Sale::where('is_returned', false)
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year);
        if ($branchFilter) {
            $salesCustomersQuery->where('branch_id', $branchFilter);
        }
        $salesCustomers = $salesCustomersQuery->count();

        return view('home', [
            // المبيعات اليوم مع فلتر الفرع
            'todaySales' => $todaySalesCount,
            // صيانات معلقة بعد خصم المسلَّمة (مصدر مرشح بحسب الفرع)
            'pendingRepairs' => $pendingRepairs,
            // عملاء الصيانة والمبيعات والمنتجات مع فلتر الفرع
            'repairCustomers' => $repairCustomers,
            'salesCustomers' => $salesCustomers,
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
    Route::middleware('section.permission:sales')->group(function () {
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

    // إرجاع عملية بيع
    Route::post('/sales/{sale}/return', [SalesController::class, 'returnSale'])->name('sales.return');
    });

    // Repairs routes
    Route::middleware('section.permission:repairs')->group(function () {
    Route::get('/repairs', [RepairsController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/create', [RepairsController::class, 'create'])->name('repairs.create');
    Route::post('/repairs', [RepairsController::class, 'store'])->name('repairs.store');
    Route::get('/repairs/{repair}/edit', [RepairsController::class, 'edit'])->name('repairs.edit');
    Route::put('/repairs/{repair}', [RepairsController::class, 'update'])->name('repairs.update');
    Route::delete('/repairs/{repair}', [RepairsController::class, 'destroy'])->name('repairs.destroy');
    });

    // Purchases routes
    Route::middleware('section.permission:purchases')->group(function () {
    Route::get('/purchases', [PurchasesController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchasesController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}/edit', [PurchasesController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}', [PurchasesController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchasesController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/purchases/create-catalog', [PurchasesController::class, 'createCatalog'])->name('purchases.create-catalog');
    Route::post('/purchases/store-catalog', [PurchasesController::class, 'storeCatalog'])->name('purchases.store-catalog');
    });

    // Catalog routes
    Route::middleware('section.permission:catalog')->group(function () {
    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/catalog/create', [CatalogController::class, 'create'])->name('catalog.create');
    Route::post('/catalog', [CatalogController::class, 'store'])->name('catalog.store');
    Route::delete('/catalog/{item}', [CatalogController::class, 'destroy'])->name('catalog.destroy');
    Route::get('/catalog/{item}/edit', [CatalogController::class, 'edit'])->name('catalog.edit');
    Route::put('/catalog/{item}', [CatalogController::class, 'update'])->name('catalog.update');
    });

    // Maintenance Deposit routes
    Route::middleware('section.permission:deposits')->group(function () {
    Route::get('/deposits', [MaintenanceDepositController::class, 'index'])->name('deposits.index');
    Route::get('/deposits/create', [MaintenanceDepositController::class, 'create'])->name('deposits.create');
    Route::post('/deposits', [MaintenanceDepositController::class, 'store'])->name('deposits.store');
    Route::delete('/deposits/{id}', [MaintenanceDepositController::class, 'destroy'])->name('deposits.destroy');
    Route::get('/deposits/{id}/edit', [MaintenanceDepositController::class, 'edit'])->name('deposits.edit');
    Route::put('/deposits/{id}', [MaintenanceDepositController::class, 'update'])->name('deposits.update');
    });

    // Reports routes
    Route::middleware('section.permission:reports')->group(function () {
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportsController::class, 'exportPdf'])->name('reports.export-pdf');
    });

    // Obligation routes
    Route::middleware('section.permission:obligations')->group(function () {
    Route::get('/obligations', [ObligationController::class, 'index'])->name('obligations.index');
    Route::get('/obligations/create', [ObligationController::class, 'create'])->name('obligations.create');
    Route::post('/obligations', [ObligationController::class, 'store'])->name('obligations.store');
    Route::get('/obligations/{obligation}/edit', [ObligationController::class, 'edit'])->name('obligations.edit');
    Route::put('/obligations/{obligation}', [ObligationController::class, 'update'])->name('obligations.update');
    Route::delete('/obligations/{obligation}', [ObligationController::class, 'destroy'])->name('obligations.destroy');
    });

    // Invoices routes
    Route::middleware('section.permission:invoices')->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/invoices/{id}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{id}/receipt', [InvoiceController::class, 'receipt'])->name('invoices.receipt');

    // مطالبة مالية (Financial Claim) — نفس صلاحية الفواتير
    Route::get('/financial-claims', [FinancialClaimController::class, 'index'])->name('financial-claims.index');
    Route::get('/financial-claims/create', [FinancialClaimController::class, 'create'])->name('financial-claims.create');
    Route::post('/financial-claims', [FinancialClaimController::class, 'store'])->name('financial-claims.store');
    Route::get('/financial-claims/{id}/print', [FinancialClaimController::class, 'print'])->name('financial-claims.print');
    Route::get('/financial-claims/{id}/download-pdf', [FinancialClaimController::class, 'downloadPdf'])->name('financial-claims.download-pdf');
    Route::delete('/financial-claims/{id}', [FinancialClaimController::class, 'destroy'])->name('financial-claims.destroy');
    });

    // صفحة المتطابقات الرئيسية
    Route::middleware('section.permission:compatibility')->group(function () {
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
    });

    // Customer Orders routes
    Route::prefix('customer-orders')->name('customer-orders.')->middleware('section.permission:customer_orders')->group(function () {
        Route::get('/', [CustomerOrderController::class, 'index'])->name('index');
        Route::get('/create', [CustomerOrderController::class, 'create'])->name('create');
        Route::post('/', [CustomerOrderController::class, 'store'])->name('store');
        Route::get('/{customerOrder}', [CustomerOrderController::class, 'show'])->name('show');
        Route::get('/{customerOrder}/edit', [CustomerOrderController::class, 'edit'])->name('edit');
        Route::put('/{customerOrder}', [CustomerOrderController::class, 'update'])->name('update');
        Route::delete('/{customerOrder}', [CustomerOrderController::class, 'destroy'])->name('destroy');
    });

    // Daily Handovers routes
    Route::prefix('daily-handovers')->name('daily-handovers.')->middleware('section.permission:daily_handovers')->group(function () {
        Route::get('/', [DailyHandoverController::class, 'index'])->name('index');
        Route::get('/create', [DailyHandoverController::class, 'create'])->name('create');
        Route::post('/', [DailyHandoverController::class, 'store'])->name('store');
        Route::get('/{dailyHandover}/edit', [DailyHandoverController::class, 'edit'])->name('edit');
        Route::put('/{dailyHandover}', [DailyHandoverController::class, 'update'])->name('update');
        Route::delete('/{dailyHandover}', [DailyHandoverController::class, 'destroy'])->name('destroy');
        Route::get('/reports', [DailyHandoverController::class, 'reports'])->name('reports');
    });

    Route::prefix('returned-goods')->name('returned-goods.')->middleware('section.permission:returned_goods')->group(function () {
        Route::get('/', [ReturnedGoodController::class, 'index'])->name('index');
        Route::get('/create', [ReturnedGoodController::class, 'create'])->name('create');
        Route::post('/', [ReturnedGoodController::class, 'store'])->name('store');
        Route::get('/{returnedGood}', [ReturnedGoodController::class, 'show'])->name('show');
        Route::get('/{returnedGood}/edit', [ReturnedGoodController::class, 'edit'])->name('edit');
        Route::put('/{returnedGood}', [ReturnedGoodController::class, 'update'])->name('update');
        Route::delete('/{returnedGood}', [ReturnedGoodController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('store')->middleware('section.permission:store')->group(function () {
        Route::get('/', [StoreController::class, 'index'])->name('store.index');
        Route::get('/create', [StoreController::class, 'create'])->name('store.create');
        Route::post('/', [StoreController::class, 'store'])->name('store.store');
        Route::get('/{id}/edit', [StoreController::class, 'edit'])->name('store.edit');
        Route::put('/{id}', [StoreController::class, 'update'])->name('store.update');
        Route::delete('/{id}', [StoreController::class, 'destroy'])->name('store.destroy');
    });
    // Debts routes
    Route::prefix('debts')->name('debts.')->middleware('section.permission:debts')->group(function () {
        Route::get('/', [DebtController::class, 'index'])->name('index');    // عرض جميع الديون
        Route::get('/create', [DebtController::class, 'create'])->name('create');  // عرض نموذج إضافة دين جديد
        Route::post('/', [DebtController::class, 'store'])->name('store');   // حفظ دين جديد
        Route::get('{debt}/edit', [DebtController::class, 'edit'])->name('edit'); // عرض نموذج تعديل دين
        Route::put('{debt}', [DebtController::class, 'update'])->name('update'); // تحديث دين
        Route::delete('{debt}', [DebtController::class, 'destroy'])->name('destroy'); // حذف دين
    });

    // Backup routes
    Route::prefix('backup')->name('backup.')->middleware('section.permission:backup')->group(function () {
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
    Route::prefix('maintenance_parts')->name('maintenance_parts.')->middleware('section.permission:maintenance_parts')->group(function () {
        Route::get('/', [MaintenancePartController::class, 'index'])->name('index');
        Route::get('/create', [MaintenancePartController::class, 'create'])->name('create');
        Route::post('/', [MaintenancePartController::class, 'store'])->name('store');
        Route::get('/{maintenancePart}', [MaintenancePartController::class, 'show'])->name('show');
        Route::get('/{maintenancePart}/edit', [MaintenancePartController::class, 'edit'])->name('edit');
        Route::put('/{maintenancePart}', [MaintenancePartController::class, 'update'])->name('update');
        Route::delete('/{maintenancePart}', [MaintenancePartController::class, 'destroy'])->name('destroy');
    });

Route::middleware('section.permission:products')->group(function () {

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

}); // نهاية حماية قسم المنتجات/اللابتوبات/البرامج


    // ⚠️ تم نقل /run-migrate و /fix-config إلى system-admin/maintenance/* (محمية بحساب مدير النظام) بدل ما تكون مفتوحة بدون تسجيل دخول

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ===================== مدير النظام (Platform Super Admin) - منفصل تماماً عن أي معرض =====================

Route::prefix('system-admin')->name('system-admin.')->group(function () {
    // إعداد أول حساب (مرة وحدة بس، بيقفل تلقائياً بعدها)
    Route::get('/setup', [PlatformSetupController::class, 'show'])->name('setup');
    Route::post('/setup', [PlatformSetupController::class, 'store'])->name('setup.submit');

    // تسجيل الدخول
    Route::get('/login', [PlatformAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PlatformAuthController::class, 'login'])->name('login.submit');

    Route::middleware('auth:platform')->group(function () {
        Route::post('/logout', [PlatformAuthController::class, 'logout'])->name('logout');

        Route::get('/', [PlatformDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('/', [TenantManagementController::class, 'index'])->name('index');
            Route::get('/create-auto', [TenantManagementController::class, 'createAuto'])->name('createAuto');
            Route::post('/create-auto', [TenantManagementController::class, 'storeAuto'])->name('storeAuto');
            Route::get('/create', [TenantManagementController::class, 'create'])->name('create');
            Route::post('/', [TenantManagementController::class, 'store'])->name('store');
            Route::get('/{tenant}/edit', [TenantManagementController::class, 'edit'])->name('edit');
            Route::put('/{tenant}', [TenantManagementController::class, 'update'])->name('update');
            Route::post('/{tenant}/migrate', [TenantManagementController::class, 'migrate'])->name('migrate');
            Route::patch('/{tenant}/toggle', [TenantManagementController::class, 'toggle'])->name('toggle');
            Route::delete('/{tenant}', [TenantManagementController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::post('/migrate', [TenantManagementController::class, 'maintenanceMigrate'])->name('migrate');
            Route::post('/clear-cache', [TenantManagementController::class, 'maintenanceClearCache'])->name('clear-cache');
            Route::post('/migrate-all', [TenantManagementController::class, 'migrateAll'])->name('migrate-all');
            Route::post('/self-test-provisioning', [TenantManagementController::class, 'selfTestProvisioning'])->name('self-test-provisioning');
        });

        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('/', [PlatformAccountController::class, 'index'])->name('index');
            Route::post('/{tenant}/users', [PlatformAccountController::class, 'storeUser'])->name('users.store');
            Route::delete('/{tenant}/users/{user}', [PlatformAccountController::class, 'destroyUser'])->name('users.destroy');
            Route::post('/{tenant}/users/{user}/toggle', [PlatformAccountController::class, 'toggleUser'])->name('users.toggle');
            Route::post('/{tenant}/users/{user}/reset-password', [PlatformAccountController::class, 'resetUserPassword'])->name('users.reset-password');
            Route::post('/{tenant}/customers', [PlatformAccountController::class, 'storeCustomer'])->name('customers.store');
            Route::delete('/{tenant}/customers/{customer}', [PlatformAccountController::class, 'destroyCustomer'])->name('customers.destroy');
            Route::post('/{tenant}/customers/{customer}/toggle', [PlatformAccountController::class, 'toggleCustomer'])->name('customers.toggle');
            Route::post('/{tenant}/customers/{customer}/reset-password', [PlatformAccountController::class, 'resetCustomerPassword'])->name('customers.reset-password');
        });

        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [PlatformSupportController::class, 'index'])->name('index');
            Route::get('/{ticket}', [PlatformSupportController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [PlatformSupportController::class, 'reply'])->name('reply');
            Route::patch('/{ticket}/status', [PlatformSupportController::class, 'updateStatus'])->name('update-status');
        });

        Route::get('/reports', [PlatformReportsController::class, 'index'])->name('reports');
    });
});

// ===================== Customer Auth & Shop (Guest customers) =====================

// تسجيل دخول/حساب العميل (Guest only)
    Route::middleware('guest:customer')->group(function () {
        Route::get('/account/register', [CustomerAuthController::class, 'showRegisterForm'])->name('customer.register');
        Route::post('/account/register', [CustomerAuthController::class, 'register']);
    
        Route::get('/account/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
        Route::post('/account/login', [CustomerAuthController::class, 'login']);
    
        // ⬇️ جديد
        Route::get('/account/forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])->name('customer.password.request');
        Route::post('/account/forgot-password', [CustomerAuthController::class, 'sendResetLinkEmail'])->name('customer.password.email');
        Route::get('/account/reset-password/{token}', [CustomerAuthController::class, 'showResetPasswordForm'])->name('customer.password.reset');
        Route::post('/account/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('customer.password.update');
    });

Route::post('/account/logout', [CustomerAuthController::class, 'logout'])
    ->middleware('auth:customer')
    ->name('customer.logout');

// السلة (متاحة لأي عميل مسجل دخول)
Route::middleware('auth:customer')->group(function () {

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
    });

    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
    });

    Route::prefix('account')->name('customer.')->group(function () {
        Route::get('/', [CustomerAccountController::class, 'show'])->name('account');
        Route::get('/profile', [CustomerAccountController::class, 'editProfile'])->name('profile.edit');
        Route::patch('/profile', [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/password', [CustomerAccountController::class, 'editPassword'])->name('password.edit');
        Route::patch('/password', [CustomerAccountController::class, 'updatePassword'])->name('password.update');
        Route::get('/orders', [CustomerAccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [CustomerAccountController::class, 'showOrder'])->name('orders.show');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');

        // إشعارات الدفع (Web Push) للزبون
        Route::post('/push/subscribe', [CustomerPushSubscriptionController::class, 'store'])->name('push.subscribe');
        Route::delete('/push/unsubscribe', [CustomerPushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    });

    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // بوابة دفع جوال باي الوهمية (مؤقتة لحد ما توصل بيانات الاعتماد الحقيقية)
    Route::prefix('payment/jawwalpay')->name('jawwalpay.')->group(function () {
        Route::get('/mock/{order}', [JawwalPayController::class, 'mockGateway'])->name('mock');
        Route::post('/mock/{order}/resolve', [JawwalPayController::class, 'mockResolve'])->name('mock.resolve');
    });
});

// صفحات دفع جوال باي (Callback يستقبل من خارج الموقع، بدون auth middleware)
Route::prefix('payment/jawwalpay')->name('jawwalpay.')->group(function () {
    Route::get('/return/{order}', [JawwalPayController::class, 'return'])->name('return');
    Route::post('/callback', [JawwalPayController::class, 'callback'])->name('callback');
});