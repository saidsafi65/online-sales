<?php

use App\Http\Controllers\SalesController;
use App\Http\Controllers\RepairsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
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

// Catalog routes
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/create', [CatalogController::class, 'create'])->name('catalog.create');
Route::post('/catalog', [CatalogController::class, 'store'])->name('catalog.store');
Route::delete('/catalog/{item}', [CatalogController::class, 'destroy'])->name('catalog.destroy');

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
