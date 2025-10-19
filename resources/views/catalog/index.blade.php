@extends('layout.app')

@section('title', 'كتالوج المنتجات')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title">📦 كتالوج المنتجات</h1>
        <p class="welcome-subtitle">إدارة وعرض منتجات المعرض</p>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
            style="border-radius: 15px; border: none; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
<style>
    .service-card {
        height: auto;
    }
</style>
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Card -->
            <div class="service-card card-primary mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1" style="color: var(--text-primary);">
                            <i class="fas fa-boxes text-primary me-2"></i>
                            قائمة المنتجات
                        </h4>
                        <p class="mb-0 text-muted">إجمالي المنتجات: <strong>{{ $items->total() }}</strong></p>
                    </div>
                    <a href="{{ route('catalog.create') }}" class="btn btn-lg"
                        style="background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(30, 64, 175, 0.3); transition: all 0.3s ease;">
                        <i class="fas fa-plus me-2"></i>
                        إضافة منتج جديد
                    </a>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="service-card card-info mb-4">
                <form method="GET" action="{{ route('catalog.index') }}">
                    <div class="row g-3">
                        <!-- Search Input -->
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-search" style="color: #0ea5e9;"></i>
                                البحث
                            </label>
                            <input type="text" name="search" value="{{ request()->search }}" class="form-control"
                                placeholder="ابحث عن منتج أو نوع..."
                                style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                        </div>

                        <!-- Quantity Filter -->
                        <div class="col-md-2">
                            <label class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-warehouse" style="color: #10b981;"></i>
                                الكمية
                            </label>
                            <select name="quantity_filter" class="form-select"
                                style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                                <option value="">الكل</option>
                                <option value="low" {{ request()->quantity_filter == 'low' ? 'selected' : '' }}>منخفض (≤ 5)</option>
                                <option value="medium" {{ request()->quantity_filter == 'medium' ? 'selected' : '' }}>متوسط (6-20)</option>
                                <option value="high" {{ request()->quantity_filter == 'high' ? 'selected' : '' }}>عالي (> 20)</option>
                            </select>
                        </div>

                        <!-- Price Min -->
                        <div class="col-md-2">
                            <label class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-money-bill-wave" style="color: #f59e0b;"></i>
                                السعر من
                            </label>
                            <input type="number" name="price_min" value="{{ request()->price_min }}" class="form-control"
                                placeholder="0" min="0" step="0.01"
                                style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                        </div>

                        <!-- Price Max -->
                        <div class="col-md-2">
                            <label class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-money-bill-wave" style="color: #f59e0b;"></i>
                                إلى
                            </label>
                            <input type="number" name="price_max" value="{{ request()->price_max }}" class="form-control"
                                placeholder="∞" min="0" step="0.01"
                                style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); color: white; padding: 0.75rem 1rem; border-radius: 10px; border: none; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <i class="fas fa-filter"></i>
                                بحث
                            </button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    @if(request()->filled('search') || request()->filled('quantity_filter') || request()->filled('price_min') || request()->filled('price_max'))
                    <div class="mt-3">
                        <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>
                            إعادة تعيين
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Table Card -->
            <div class="service-card card-success">
                <div style="overflow-x: auto; border-radius: 12px; border: 2px solid #e2e8f0;">
                    <table class="table mb-0" style="min-width: 1000px;">
                        <thead style="background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); color: white;">
                            <tr>
                                <th style="padding: 1rem; text-align: center; width: 60px;">#</th>
                                <th style="padding: 1rem;">اسم المنتج</th>
                                <th style="padding: 1rem;">النوع / الموديل</th>
                                <th style="padding: 1rem; text-align: center; width: 100px;">الكمية</th>
                                <th style="padding: 1rem; text-align: center; width: 130px;">سعر الجملة</th>
                                <th style="padding: 1rem; text-align: center; width: 130px;">سعر البيع</th>
                                <th style="padding: 1rem; text-align: center; width: 150px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr style="transition: all 0.2s ease; 
                                    @if($item->quantity == 0)
                                        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
                                        border-left: 4px solid #ef4444;
                                    @elseif($item->quantity <= 5)
                                        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                                    @endif
                                ">
                                    <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                        <span
                                            style="background: #1e40af; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">
                                            {{ ($items->currentPage() - 1) * $items->perPage() + $index + 1 }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem; vertical-align: middle;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            @if($item->quantity == 0)
                                                <i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>
                                            @else
                                                <i class="fas fa-box" style="color: #1e40af;"></i>
                                            @endif
                                            <span style="font-weight: 600; color: #1e293b;">{{ $item->product }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem; vertical-align: middle;">
                                        <span
                                            style="background: #dbeafe; color: #1e40af; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.9rem; font-weight: 500;">
                                            {{ $item->type }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                        <span
                                            style="background: {{ $item->quantity == 0 ? '#fee2e2' : ($item->quantity > 10 ? '#d1fae5' : '#fef3c7') }}; 
                                                    color: {{ $item->quantity == 0 ? '#dc2626' : ($item->quantity > 10 ? '#065f46' : '#b45309') }}; 
                                                    padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; font-size: 1.1rem;
                                                    @if($item->quantity == 0) border: 2px solid #ef4444; @endif
                                            ">
                                            @if($item->quantity == 0)
                                                <i class="fas fa-ban me-1"></i>
                                            @endif
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                        <div style="font-weight: 600; color: #059669; font-size: 1rem;">
                                            {{ number_format($item->wholesale_price, 2) }} ₪
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                        <div style="font-weight: 700; color: #10b981; font-size: 1.1rem;">
                                            {{ number_format($item->sale_price, 2) }} ₪
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                            <a href="{{ route('catalog.edit', $item->id) }}" class="btn btn-sm"
                                                style="background: #0ea5e9; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; transition: all 0.3s ease;"
                                                title="تعديل"
                                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.4)';"
                                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('catalog.destroy', $item) }}" method="POST"
                                                style="margin: 0; display: inline;"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm"
                                                    style="background: #ef4444; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; transition: all 0.3s ease; cursor: pointer;"
                                                    title="حذف"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.4)';"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 3rem; text-align: center;">
                                        <div style="color: #94a3b8;">
                                            <i class="fas fa-box-open"
                                                style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                            <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">لا توجد منتجات</p>
                                            <p style="font-size: 0.9rem;">ابدأ بإضافة منتج جديد للكتالوج</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($items->hasPages())
                    <div style="margin-top: 2rem; display: flex; justify-content: center;">
                        {{ $items->links() }}
                    </div>
                @endif
                
            </div>

            <!-- Statistics Cards -->
            @if ($items->count() > 0)
                <div class="row g-4 mt-4">
                    <div class="col-md-3">
                        <div class="service-card card-primary" style="text-align: center; padding: 1.5rem;">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #1e40af; font-size: 1.8rem;">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700;">
                                {{ $items->total() }}
                            </h3>
                            <p style="color: #64748b; margin: 0; font-weight: 500;">إجمالي المنتجات</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="service-card card-success" style="text-align: center; padding: 1.5rem;">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #10b981; font-size: 1.8rem;">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700;">
                                {{ $items->sum('quantity') }}
                            </h3>
                            <p style="color: #64748b; margin: 0; font-weight: 500;">إجمالي الكمية</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="service-card card-warning" style="text-align: center; padding: 1.5rem;">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #ef4444; font-size: 1.8rem;">
                                <i class="fas fa-ban"></i>
                            </div>
                            <h3 style="color: #dc2626; margin-bottom: 0.5rem; font-size: 1.8rem; font-weight: 700;">
                                {{ $items->where('quantity', 0)->count() }}
                            </h3>
                            <p style="color: #64748b; margin: 0; font-weight: 500;">منتجات بدون مخزون</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="service-card card-info" style="text-align: center; padding: 1.5rem;">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #0ea5e9; font-size: 1.8rem;">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 1.5rem; font-weight: 700;">
                                {{ number_format($items->sum(function($item) { return $item->quantity * $item->sale_price; }), 2) }} ₪
                            </h3>
                            <p style="color: #64748b; margin: 0; font-weight: 500;">قيمة المخزون</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .table tbody tr:hover {
                background: #f8fafc !important;
            }

            .alert {
                animation: slideInDown 0.5s ease-out;
            }

            @keyframes slideInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .pagination .page-link {
                border-radius: 8px;
                margin: 0 0.25rem;
                border: none;
                color: #1e40af;
                font-weight: 600;
                padding: 0.5rem 1rem;
            }

            .pagination .page-item.active .page-link {
                background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
                color: white;
            }

            .pagination .page-link:hover {
                background: #f1f5f9;
                color: #1e40af;
            }
        </style>
    @endpush
@endsection