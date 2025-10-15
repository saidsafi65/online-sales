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
            <div class="search-filter-section mb-4">
                <form method="GET" action="{{ route('catalog.index') }}">
                    <div class="row">
                        <!-- Search Input -->
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ request()->search }}" class="form-control"
                                placeholder="البحث بالاسم أو النوع">
                        </div>

                        <!-- Quantity Filter -->
                        <div class="col-md-3">
                            <select name="quantity_filter" class="form-control">
                                <option value="">تصفية حسب الكمية</option>
                                <option value="low" {{ request()->quantity_filter == 'low' ? 'selected' : '' }}>منخفضة
                                    (0-5)</option>
                                <option value="medium" {{ request()->quantity_filter == 'medium' ? 'selected' : '' }}>متوسطة
                                    (6-20)</option>
                                <option value="high" {{ request()->quantity_filter == 'high' ? 'selected' : '' }}>عالية
                                    (>20)</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="col-md-2">
                            <input type="number" name="price_min" value="{{ request()->price_min }}" class="form-control"
                                placeholder="السعر الأدنى" min="0">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="price_max" value="{{ request()->price_max }}" class="form-control"
                                placeholder="السعر الأعلى" min="0">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">بحث</button>
                        </div>
                    </div>
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
                                <tr style="transition: all 0.2s ease;">
                                    <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                        <span
                                            style="background: #1e40af; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">
                                            {{ ($items->currentPage() - 1) * $items->perPage() + $index + 1 }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem; vertical-align: middle;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <i class="fas fa-box" style="color: #1e40af;"></i>
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
                                            style="background: {{ $item->quantity > 10 ? '#d1fae5' : '#fef3c7' }}; color: {{ $item->quantity > 10 ? '#065f46' : '#b45309' }}; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; font-size: 1.1rem;">
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
                                            <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">لا توجد
                                                منتجات</p>
                                            <p style="font-size: 0.9rem;">ابدأ بإضافة منتج جديد للكتالوج</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <style>
                    .pagination-wrapper a,
                    .pagination-wrapper span {
                        display: inline-block;
                        padding: 8px 16px;
                        margin: 0 4px;
                        background-color: #f0f0f0;
                        border-radius: 4px;
                        color: #333;
                        font-weight: 600;
                        text-decoration: none;
                        transition: all 0.3s ease;
                    }

                    .pagination-wrapper a:hover {
                        background-color: #6366f1;
                        color: #fff;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    }

                    .pagination-wrapper .active {
                        background-color: #6366f1;
                        color: white;
                        font-weight: 700;
                    }

                    .pagination-wrapper span {
                        background-color: #ddd;
                        color: #777;
                    }
                </style>

                <!-- Pagination -->
                @if ($items->hasPages())
                    <div class="compact-pagination-container">
                        <nav class="pagination-wrapper" aria-label="Pagination">
                            {{ $items->links() }}
                        </nav>
                    </div>
                @endif
            </div>

            <!-- Table Card -->
            <div class="service-card card-success">
                <div style="overflow-x: auto; border-radius: 12px; border: 2px solid #e2e8f0;">
                    <table class="table mb-0" style="min-width: 1000px;">
                        <thead style="background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); color: white;">
                            <!-- Add the table header here -->
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr>
                                    <!-- Add table rows for each product -->
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 3rem; text-align: center;">
                                        <div style="color: #94a3b8;">
                                            <i class="fas fa-box-open"
                                                style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                            <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">لا توجد
                                                نتائج</p>
                                            <p style="font-size: 0.9rem;">حاول تعديل الفلاتر أو إضافة منتجات جديدة</p>
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
                <div class="row g-4 mt-2">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="service-card card-warning" style="text-align: center; padding: 1.5rem;">
                            <div
                                style="width: 60px; height: 60px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #f59e0b; font-size: 1.8rem;">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 1.5rem; font-weight: 700;">
                                {{ number_format($totalInventoryValue, 2) }} ₪
                            </h3>
                            <p style="color: #64748b; margin: 0; font-weight: 500;">قيمة المخزون (سعر البيع)</p>
                            <div style="margin-top: 0.5rem; font-size: 0.95rem; color: #475569;">
                                إجمالي حسب سعر الجملة: <strong>{{ number_format($totalWholesaleValue, 2) }} ₪</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .table tbody tr:hover {
                background: #f8fafc;
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
