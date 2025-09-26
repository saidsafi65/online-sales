<?php

use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('sales.index');
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
