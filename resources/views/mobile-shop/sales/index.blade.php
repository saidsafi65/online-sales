@extends('layout.app')

@section('title', 'المبيعات - معرض الجوال')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                        <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                            <i class="fas fa-shopping-cart" style="color: #10b981; margin-right: 0.5rem;"></i>
                            المبيعات
                        </h1>
                    </div>
                    <a href="{{ route('mobile-shop.sales.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> إضافة مبيعة جديدة
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
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">نوع المنتج</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">الكمية</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">طريقة الدفع</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">نقدي</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">بنكي</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">الإجمالي</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">التاريخ</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ $sale->product_name }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ $sale->product_type }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ $sale->quantity }}</td>
                                <td style="padding: 1rem;">
                                    <span class="badge bg-{{ $sale->payment_method === 'نقدي' ? 'success' : ($sale->payment_method === 'تطبيق' ? 'info' : 'warning') }}">
                                        {{ $sale->payment_method }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format($sale->cash_amount, 2) }} شيكل</td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format($sale->bank_amount, 2) }} شيكل</td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format($sale->cash_amount + $sale->bank_amount, 2) }} شيكل</td>
                                <td style="padding: 1rem; color: #475569;">{{ $sale->created_at->format('Y-m-d') }}</td>
                                <td style="padding: 1rem;">
                                    <a href="{{ route('mobile-shop.sales.edit', $sale) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('mobile-shop.sales.destroy', $sale) }}" method="POST" style="display: inline;">
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
                                <td colspan="9" class="text-center py-4" style="color: #94a3b8;">
                                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <p>لا توجد مبيعات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
@endsection
