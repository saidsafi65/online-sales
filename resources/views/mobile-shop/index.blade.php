@extends('layout.app')

@section('title', 'معرض الجوال')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                    <i class="fas fa-mobile-alt" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                    معرض الجوال
                </h1>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <!-- الصيانة -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="{{ route('mobile-shop.maintenance.index') }}" class="text-decoration-none">
                    <div class="card h-100" style="border: 2px solid #f59e0b; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h6 style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">الصيانة</h6>
                                    <div style="font-size: 2rem; font-weight: 800; color: #f59e0b;">{{ $maintenanceCount }}</div>
                                    <div style="color: #94a3b8; font-size: 0.85rem; margin-top: 0.5rem;">إجمالي: {{ number_format($totalMaintenance, 2) }} شيكل</div>
                                </div>
                                <div style="font-size: 2.5rem; color: #f59e0b; opacity: 0.2;">
                                    <i class="fas fa-tools"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- المبيعات -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="{{ route('mobile-shop.sales.index') }}" class="text-decoration-none">
                    <div class="card h-100" style="border: 2px solid #10b981; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h6 style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">المبيعات</h6>
                                    <div style="font-size: 2rem; font-weight: 800; color: #10b981;">{{ $salesCount }}</div>
                                    <div style="color: #94a3b8; font-size: 0.85rem; margin-top: 0.5rem;">إجمالي: {{ number_format($totalSales, 2) }} شيكل</div>
                                </div>
                                <div style="font-size: 2.5rem; color: #10b981; opacity: 0.2;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- المخزون -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="{{ route('mobile-shop.inventory.index') }}" class="text-decoration-none">
                    <div class="card h-100" style="border: 2px solid #3b82f6; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h6 style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">المنتجات في المخزن</h6>
                                    <div style="font-size: 2rem; font-weight: 800; color: #3b82f6;">{{ $inventoryCount }}</div>
                                    <div style="color: #94a3b8; font-size: 0.85rem; margin-top: 0.5rem;">وحدة</div>
                                </div>
                                <div style="font-size: 2.5rem; color: #3b82f6; opacity: 0.2;">
                                    <i class="fas fa-boxes"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- الديون -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="{{ route('mobile-shop.debts.index') }}" class="text-decoration-none">
                    <div class="card h-100" style="border: 2px solid #ef4444; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h6 style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">الديون</h6>
                                    <div style="font-size: 2rem; font-weight: 800; color: #ef4444;">{{ $debtsCount }}</div>
                                    <div style="color: #94a3b8; font-size: 0.85rem; margin-top: 0.5rem;">إجمالي: {{ number_format($totalDebts, 2) }} شيكل</div>
                                </div>
                                <div style="font-size: 2.5rem; color: #ef4444; opacity: 0.2;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- المصروفات -->
            <div class="col-12 col-sm-6 col-lg-4">
                <a href="{{ route('mobile-shop.expenses.index') }}" class="text-decoration-none">
                    <div class="card h-100" style="border: 2px solid #8b5cf6; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h6 style="color: #64748b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">المصروفات</h6>
                                    <div style="font-size: 2rem; font-weight: 800; color: #8b5cf6;">{{ $expensesCount }}</div>
                                    <div style="color: #94a3b8; font-size: 0.85rem; margin-top: 0.5rem;">إجمالي: {{ number_format($totalExpenses, 2) }} شيكل</div>
                                </div>
                                <div style="font-size: 2.5rem; color: #8b5cf6; opacity: 0.2;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div class="card-body">
                        <h6 style="color: #1e293b; font-weight: 700; margin-bottom: 1rem;">إجراءات سريعة</h6>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <a href="{{ route('mobile-shop.maintenance.create') }}" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-plus"></i> إضافة صيانة
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <a href="{{ route('mobile-shop.sales.create') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-plus"></i> إضافة مبيعة
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <a href="{{ route('mobile-shop.inventory.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-plus"></i> إضافة منتج
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <a href="{{ route('mobile-shop.expenses.create') }}" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-plus"></i> إضافة مصروف
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
