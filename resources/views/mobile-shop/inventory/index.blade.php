@extends('layout.app')

@section('title', 'المنتجات في المخزن - معرض الجوال')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                        <i class="fas fa-boxes" style="color: #3b82f6; margin-right: 0.5rem;"></i>
                        المنتجات في المخزن
                    </h1>
                    <a href="{{ route('mobile-shop.inventory.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة منتج جديد
                    </a>
                </div>
            </div>
        </div>

        @if ($message = session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Table -->
        <div class="card" style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">اسم المنتج</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">النوع / الموديل</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">الكمية</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">سعر الجملة</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">سعر البيع</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventory as $item)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ $item->product_name }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ $item->model_type }}</td>
                                <td style="padding: 1rem; color: #475569;">
                                    <span class="badge bg-info">{{ $item->quantity }}</span>
                                </td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format($item->wholesale_price, 2) }} شيكل</td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format($item->selling_price, 2) }} شيكل</td>
                                <td style="padding: 1rem;">
                                    <a href="{{ route('mobile-shop.inventory.edit', $item) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('mobile-shop.inventory.destroy', $item) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4" style="color: #94a3b8;">
                                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <p>لا توجد منتجات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $inventory->links() }}
        </div>
    </div>
@endsection
