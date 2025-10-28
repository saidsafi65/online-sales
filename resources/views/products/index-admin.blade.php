@extends('layout.app')

@section('title', 'إدارة المنتجات')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">📦 إدارة المنتجات</h1>
    <p class="welcome-subtitle">أضف وعدّل وحذف منتجات متجرك</p>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" 
     style="border-radius: 15px; border: none; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; margin-bottom: 2rem;">
    <i class="fas fa-check-circle me-2"></i>
    <strong>{{ session('success') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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
                    <p class="mb-0 text-muted">إجمالي المنتجات: <strong>{{ $products->total() }}</strong></p>
                </div>
                <a href="{{ route('products.create') }}" class="btn btn-lg" 
                   style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); transition: all 0.3s ease;">
                    <i class="fas fa-plus me-2"></i>
                    إضافة منتج جديد
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="service-card card-success">
            <div style="overflow-x: auto; border-radius: 12px; border: 2px solid #e2e8f0;">
                <table class="table mb-0" style="min-width: 1000px;">
                    <thead style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white;">
                        <tr>
                            <th style="padding: 1rem; text-align: center; width: 60px;">#</th>
                            <th style="padding: 1rem; width: 120px;">الصورة</th>
                            <th style="padding: 1rem;">اسم المنتج</th>
                            <th style="padding: 1rem; text-align: center; width: 150px;">السعر</th>
                            <th style="padding: 1rem; text-align: center; width: 200px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr style="transition: all 0.2s ease;">
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <span style="background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">
                                        {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem; vertical-align: middle;">
                                    @if($product->image && file_exists(storage_path('app/public/' . $product->image)))
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}"
                                             style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                                    @else
                                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #6366f1; font-size: 1.5rem;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-box" style="color: #10b981;"></i>
                                        <div>
                                            <p style="margin: 0; font-weight: 600; color: #1e293b;">{{ $product->name }}</p>
                                            @if($product->description)
                                                <p style="margin: 0.25rem 0 0 0; color: #64748b; font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                                    {{ $product->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <span style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; font-size: 1rem;">
                                        {{ number_format($product->price, 2) }} شيكل
                                    </span>
                                </td>
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="{{ route('products.show', $product->id) }}" 
                                           class="btn btn-sm" 
                                           style="background: #0ea5e9; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; transition: all 0.3s ease; text-decoration: none;"
                                           title="عرض"
                                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.4)';"
                                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product->id) }}" 
                                           class="btn btn-sm" 
                                           style="background: #8b5cf6; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; transition: all 0.3s ease; text-decoration: none;"
                                           title="تعديل"
                                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.4)';"
                                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" 
                                              method="POST" 
                                              style="margin: 0; display: inline;"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm" 
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
                                <td colspan="5" style="padding: 3rem; text-align: center;">
                                    <div style="color: #94a3b8;">
                                        <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                        <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">لا توجد منتجات</p>
                                        <p style="font-size: 0.9rem;">ابدأ بإضافة منتج جديد</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $products->links() }}
            </div>
            @endif
        </div>

        <!-- Statistics Cards -->
        @if($products->count() > 0)
        <div class="row g-4 mt-2">
            <div class="col-md-3">
                <div class="service-card card-primary" style="text-align: center; padding: 1.5rem;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #1e40af; font-size: 1.8rem;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700;">
                        {{ $products->total() }}
                    </h3>
                    <p style="color: #64748b; margin: 0; font-weight: 500;">إجمالي المنتجات</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="service-card card-success" style="text-align: center; padding: 1.5rem;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #10b981; font-size: 1.8rem;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 1.8rem; font-weight: 700;">
                        {{ number_format($products->sum('price'), 2) }}
                    </h3>
                    <p style="color: #64748b; margin: 0; font-weight: 500;">إجمالي القيمة</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="service-card card-info" style="text-align: center; padding: 1.5rem;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #0ea5e9; font-size: 1.8rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 1.8rem; font-weight: 700;">
                        {{ number_format($products->avg('price'), 2) }}
                    </h3>
                    <p style="color: #64748b; margin: 0; font-weight: 500;">متوسط السعر</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="service-card card-warning" style="text-align: center; padding: 1.5rem;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #f59e0b; font-size: 1.8rem;">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 1.8rem; font-weight: 700;">
                        {{ $products->count() }}
                    </h3>
                    <p style="color: #64748b; margin: 0; font-weight: 500;">منتجات هذه الصفحة</p>
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