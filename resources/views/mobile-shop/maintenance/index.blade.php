@extends('layout.app')

@section('title', 'الصيانة - معرض الجوال')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h1 class="h3 mb-0" style="color: #1e293b; font-weight: 700;">
                        <i class="fas fa-tools" style="color: #f59e0b; margin-right: 0.5rem;"></i>
                        الصيانة
                    </h1>
                    <a href="{{ route('mobile-shop.maintenance.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus"></i> إضافة صيانة جديدة
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
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">اسم الزبون</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">رقم الجوال</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">نوع الجوال</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">المشكلة</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">طريقة الدفع</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">نقدي</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">بنكي</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">الإجمالي</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">تاريخ التسليم</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">تاريخ الاستلام</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">التاريخ</th>
                            <th style="color: #475569; font-weight: 700; padding: 1rem;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($maintenances as $maintenance)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ $maintenance->customer_name }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ $maintenance->phone_number }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ $maintenance->mobile_type }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ substr($maintenance->problem_description, 0, 30) }}{{ strlen($maintenance->problem_description) > 30 ? '...' : '' }}</td>
                                <td style="padding: 1rem;">
                                    <span class="badge bg-{{ $maintenance->payment_method === 'نقدي' ? 'success' : ($maintenance->payment_method === 'تطبيق' ? 'info' : 'warning') }}">
                                        {{ $maintenance->payment_method }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format($maintenance->cash_amount ?? $maintenance->cost ?? 0, 2) }} شيكل</td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format($maintenance->bank_amount ?? 0, 2) }} شيكل</td>
                                <td style="padding: 1rem; color: #1e293b; font-weight: 600;">{{ number_format(($maintenance->cash_amount ?? $maintenance->cost ?? 0) + ($maintenance->bank_amount ?? 0), 2) }} شيكل</td>
                                <td style="padding: 1rem; color: #475569;">{{ $maintenance->delivery_date ? $maintenance->delivery_date->format('Y-m-d') : '-' }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ $maintenance->receipt_date ? $maintenance->receipt_date->format('Y-m-d') : '-' }}</td>
                                <td style="padding: 1rem; color: #475569;">{{ $maintenance->created_at->format('Y-m-d') }}</td>
                                <td style="padding: 1rem;">
                                    <a href="{{ route('mobile-shop.maintenance.edit', $maintenance) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('mobile-shop.maintenance.destroy', $maintenance) }}" method="POST" style="display: inline;">
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
                                <td colspan="12" class="text-center py-4" style="color: #94a3b8;">
                                    <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <p>لا توجد صيانات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $maintenances->links() }}
        </div>
    </div>
@endsection
