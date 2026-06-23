@extends('layout.app')

@section('title', 'إدارة المنتجات')

@push('styles')
<style>
    .admin-header {
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(30, 64, 175, 0.2);
    }

    .admin-title {
        font-size: 2rem;
        font-weight: 900;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .admin-subtitle {
        font-size: 1rem;
        opacity: 0.9;
    }

    .btn-add-product {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }

    .btn-add-product:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .products-table-wrapper {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
    }

    .products-table thead {
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        color: white;
        font-weight: 700;
    }

    .products-table th {
        padding: 1.25rem;
        text-align: right;
        font-size: 1rem;
    }

    .products-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .products-table tbody tr:hover {
        background: #f8fafc;
    }

    .products-table tbody tr:last-child {
        border-bottom: none;
    }

    .products-table td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }

    .product-row-id {
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        display: inline-block;
    }

    .product-image-cell {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .product-image-placeholder {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6366f1;
        font-size: 1.8rem;
    }

    .product-name {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .product-category {
        font-size: 0.85rem;
        color: #6366f1;
        font-weight: 600;
    }

    .product-price-badge {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        display: inline-block;
    }

    .discount-badge {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #b45309;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        display: inline-block;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.6rem 1rem;
        border-radius: 8px;
        border: none;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .btn-edit {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(139, 92, 246, 0.4);
        color: white;
    }

    .btn-delete {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.8rem;
        color: white;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 900;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.95rem;
        color: #64748b;
        font-weight: 500;
    }

    .pagination {
        margin-top: 2rem;
    }

    @media (max-width: 768px) {
        .products-table {
            font-size: 0.9rem;
        }

        .products-table th,
        .products-table td {
            padding: 0.75rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Header -->
    <div class="admin-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;">
            <div>
                <h1 class="admin-title">
                    <i class="fas fa-boxes"></i>
                    إدارة المنتجات
                </h1>
                <p class="admin-subtitle">إدارة شاملة لمنتجات متجرك - إضافة وتعديل وحذف</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn-add-product">
                <i class="fas fa-plus-circle"></i>
                <span>إضافة منتج جديد</span>
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" 
         style="border-radius: 12px; border: none; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; margin-bottom: 2rem; padding: 1rem 1.5rem;">
        <i class="fas fa-check-circle me-2"></i>
        <strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: brightness(0) saturate(100%)"></button>
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" 
         style="border-radius: 12px; border: none; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; margin-bottom: 2rem; padding: 1rem 1.5rem;">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>{{ session('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: brightness(0) saturate(100%)"></button>
    </div>
    @endif

    <!-- Products Table -->
    @if($products->count() > 0)
        <div class="products-table-wrapper">
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th style="width: 100px;">الصورة</th>
                            <th>اسم المنتج</th>
                            <th style="width: 120px;">التصنيف</th>
                            <th style="width: 120px;">السعر</th>
                            <th style="width: 100px;">الخصم</th>
                            <th style="width: 150px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr>
                                <td>
                                    <span class="product-row-id">
                                        {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->image && file_exists(storage_path('app/public/' . $product->image)))
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}"
                                             class="product-image-cell">
                                    @else
                                        <div class="product-image-placeholder">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="product-name">{{ $product->name }}</div>
                                    @if($product->description)
                                        <div style="font-size: 0.85rem; color: #64748b; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $product->description }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="product-category">{{ $product->category }}</span>
                                </td>
                                <td>
                                    <span class="product-price-badge">
                                        {{ number_format($product->price, 2) }} شيكل
                                    </span>
                                </td>
                                <td>
                                    @if($product->discount > 0)
                                        <span class="discount-badge">
                                            <i class="fas fa-percentage"></i>
                                            {{ $product->discount }}%
                                        </span>
                                    @else
                                        <span style="color: #94a3b8; font-size: 0.9rem;">بدون خصم</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('products.edit', $product->id) }}" 
                                           class="btn-action btn-edit"
                                           title="تعديل المنتج">
                                            <i class="fas fa-edit"></i>
                                            <span>تعديل</span>
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" 
                                              method="POST" 
                                              style="margin: 0; display: inline;"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn-action btn-delete"
                                                    title="حذف المنتج">
                                                <i class="fas fa-trash"></i>
                                                <span>حذف</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-box-open"></i>
                                        </div>
                                        <h3 style="margin-bottom: 0.5rem; color: #1e293b;">لا توجد منتجات</h3>
                                        <p style="margin: 0;">ابدأ بإضافة منتج جديد الآن</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="pagination" style="margin: 2rem 1.5rem 0;">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        <!-- Statistics -->
        @if($products->count() > 0)
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stat-value">{{ $products->total() }}</div>
                    <div class="stat-label">إجمالي المنتجات</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #10b981;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-value">{{ number_format($products->sum('price'), 2) }}</div>
                    <div class="stat-label">إجمالي القيمة (شيكل)</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); color: #0ea5e9;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-value">{{ number_format($products->avg('price'), 2) }}</div>
                    <div class="stat-label">متوسط السعر (شيكل)</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #f59e0b;">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="stat-value">{{ $products->where('discount', '>', 0)->count() }}</div>
                    <div class="stat-label">المنتجات المخصومة</div>
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="products-table-wrapper">
            <div class="empty-state" style="padding: 4rem 2rem;">
                <div class="empty-state-icon" style="font-size: 4rem;">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 style="color: #1e293b; font-size: 1.5rem; margin-bottom: 0.5rem;">لا توجد منتجات بعد</h3>
                <p style="color: #64748b; margin-bottom: 1.5rem;">ابدأ بإضافة منتجات جديدة لعرضها في متجرك</p>
                <a href="{{ route('products.create') }}" class="btn-add-product">
                    <i class="fas fa-plus-circle"></i>
                    <span>إضافة أول منتج</span>
                </a>
            </div>
        </div>
    @endif
</div>

@endpush
@endsection
