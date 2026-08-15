@extends('layout.app')

@section('title', 'إدارة اللابتوبات')

@push('styles')
<style>
    .admin-header {
        background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        color: white; padding: 2rem; border-radius: 20px; margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }
    .admin-title { font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 1rem; }
    .admin-subtitle { font-size: 1rem; opacity: 0.9; }
    .btn-add-product {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: 0.875rem 2rem;
        border-radius: 50px; border: none; font-weight: 600; text-decoration: none; transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); display: inline-flex; align-items: center; gap: 0.75rem; cursor: pointer;
    }
    .btn-add-product:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); color: white; }
    .products-table-wrapper { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,.08); margin-bottom: 2rem; }
    .table-responsive { overflow-x: auto; }
    .products-table { width: 100%; border-collapse: collapse; }
    .products-table thead { background: linear-gradient(135deg, #0f172a 0%, #334155 100%); color: white; font-weight: 700; }
    .products-table th { padding: 1.25rem; text-align: right; font-size: 1rem; }
    .products-table tbody tr { border-bottom: 1px solid #e2e8f0; transition: all .2s ease; }
    .products-table tbody tr:hover { background: #f8fafc; }
    .products-table tbody tr:last-child { border-bottom: none; }
    .products-table td { padding: 1rem 1.25rem; vertical-align: middle; }
    .product-row-id { background: linear-gradient(135deg, #0f172a 0%, #334155 100%); color: white; padding: .5rem 1rem; border-radius: 8px; font-weight: 700; display: inline-block; }
    .product-image-cell { width: 70px; height: 70px; border-radius: 12px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .product-image-placeholder { width: 70px; height: 70px; background: linear-gradient(135deg,#e2e8f0 0%,#cbd5e1 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #475569; font-size: 1.6rem; }
    .product-name { font-weight: 700; color: #1e293b; margin-bottom: .2rem; }
    .spec-mini { font-size: .78rem; color: #64748b; }
    .product-price-badge { background: linear-gradient(135deg,#dbeafe 0%,#bfdbfe 100%); color: #1e40af; padding: .5rem 1rem; border-radius: 8px; font-weight: 700; display: inline-block; }
    .discount-badge { background: linear-gradient(135deg,#fef3c7 0%,#fde68a 100%); color: #b45309; padding: .5rem 1rem; border-radius: 8px; font-weight: 700; display: inline-block; }
    .action-buttons { display: flex; gap: .5rem; flex-wrap: wrap; }
    .btn-action { padding: .6rem 1rem; border-radius: 8px; border: none; color: white; font-weight: 600; cursor: pointer; transition: all .3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: .5rem; font-size: .9rem; }
    .btn-edit { background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%); box-shadow: 0 4px 12px rgba(139,92,246,.3); }
    .btn-edit:hover { transform: translateY(-2px); color: white; }
    .btn-delete { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); box-shadow: 0 4px 12px rgba(239,68,68,.3); }
    .btn-delete:hover { transform: translateY(-2px); }
    .empty-state { text-align: center; padding: 3rem; color: #64748b; }
    .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; opacity: .5; }
    @media (max-width: 768px) {
        .products-table { font-size: .9rem; }
        .products-table th, .products-table td { padding: .75rem; }
        .action-buttons { flex-direction: column; }
        .btn-action { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="admin-header">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:2rem;">
            <div>
                <h1 class="admin-title"><i class="fas fa-laptop"></i> إدارة اللابتوبات</h1>
                <p class="admin-subtitle">إضافة وتعديل وحذف اللابتوبات المعروضة للزبائن</p>
            </div>
            <a href="{{ route('laptops.create') }}" class="btn-add-product">
                <i class="fas fa-plus-circle"></i><span>إضافة لابتوب جديد</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius:12px;border:none;background:linear-gradient(135deg,#d1fae5 0%,#a7f3d0 100%);color:#065f46;margin-bottom:2rem;padding:1rem 1.5rem;">
        <i class="fas fa-check-circle me-2"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($laptops->count() > 0)
        <div class="products-table-wrapper">
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th style="width:90px;">الصورة</th>
                            <th>اللابتوب</th>
                            <th style="width:140px;">المواصفات</th>
                            <th style="width:120px;">السعر</th>
                            <th style="width:100px;">الخصم</th>
                            <th style="width:150px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laptops as $index => $laptop)
                            <tr>
                                <td><span class="product-row-id">{{ ($laptops->currentPage()-1)*$laptops->perPage() + $index + 1 }}</span></td>
                                <td>
                                    @if($laptop->mainImage)
                                        <img src="{{ asset('storage/'.$laptop->mainImage->image) }}" alt="{{ $laptop->name }}" class="product-image-cell">
                                    @else
                                        <div class="product-image-placeholder"><i class="fas fa-laptop"></i></div>
                                    @endif
                                </td>
                                <td>
                                    <div class="product-name">{{ $laptop->name }}</div>
                                    <div class="spec-mini">{{ $laptop->brand }} @if($laptop->model) - {{ $laptop->model }} @endif</div>
                                    @if($laptop->is_out_of_stock)
                                        <span style="color:#dc2626;font-size:.75rem;font-weight:700;">نفذت الكمية</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="spec-mini">{{ $laptop->processor }}</div>
                                    <div class="spec-mini">{{ $laptop->ram }} / {{ $laptop->storage }}</div>
                                </td>
                                <td><span class="product-price-badge">{{ number_format($laptop->price, 2) }} شيكل</span></td>
                                <td>
                                    @if($laptop->discount > 0)
                                        <span class="discount-badge"><i class="fas fa-percentage"></i> {{ $laptop->discount }}%</span>
                                    @else
                                        <span style="color:#94a3b8;font-size:.9rem;">بدون خصم</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('laptops.edit', $laptop->id) }}" class="btn-action btn-edit"><i class="fas fa-edit"></i><span>تعديل</span></a>
                                        <form action="{{ route('laptops.destroy', $laptop->id) }}" method="POST" style="margin:0;display:inline;"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا اللابتوب؟');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete"><i class="fas fa-trash"></i><span>حذف</span></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($laptops->hasPages())
                <div style="margin:2rem 1.5rem 0;">{{ $laptops->links() }}</div>
            @endif
        </div>
    @else
        <div class="products-table-wrapper">
            <div class="empty-state" style="padding:4rem 2rem;">
                <div class="empty-state-icon" style="font-size:4rem;"><i class="fas fa-laptop"></i></div>
                <h3 style="color:#1e293b;font-size:1.5rem;margin-bottom:.5rem;">لا توجد لابتوبات بعد</h3>
                <p style="color:#64748b;margin-bottom:1.5rem;">ابدأ بإضافة أول لابتوب لعرضه للزبائن</p>
                <a href="{{ route('laptops.create') }}" class="btn-add-product"><i class="fas fa-plus-circle"></i><span>إضافة لابتوب</span></a>
            </div>
        </div>
    @endif
</div>
@endsection
