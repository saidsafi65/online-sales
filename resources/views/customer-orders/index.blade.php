@extends('layout.app')

@section('title', 'طلبات الزبائن')

            @php
                use Illuminate\Support\Str;
            @endphp
            
@push('styles')
<style>
    .orders-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    .btn-add-order {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        border: none;
    }

    .btn-add-order:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-icon.pending {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #f59e0b;
    }

    .stat-icon.in-progress {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #0ea5e9;
    }

    .stat-icon.completed {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #10b981;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .table-container {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 2px solid var(--border-color);
    }

    .orders-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .orders-table thead tr {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
    }

    .orders-table thead th {
        padding: 1rem;
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        text-align: center;
        border: none;
        white-space: nowrap;
    }

    .orders-table thead th:first-child {
        border-radius: 12px 0 0 0;
    }

    .orders-table thead th:last-child {
        border-radius: 0 12px 0 0;
    }

    .orders-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .orders-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .orders-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        text-align: center;
    }

    .customer-info {
        text-align: right;
    }

    .customer-name {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .customer-phone {
        color: var(--text-secondary);
        font-size: 0.875rem;
        direction: ltr;
        text-align: right;
    }

    .device-info {
        text-align: right;
    }

    .device-type {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .device-specs {
        color: var(--text-secondary);
        font-size: 0.85rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    .price-badge {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.95rem;
        display: inline-block;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
    }

    .status-badge.pending {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }

    .status-badge.in-progress {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e3a8a;
    }

    .status-badge.completed {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-badge.cancelled {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .btn-action {
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-view {
        background: rgba(30, 64, 175, 0.1);
        color: var(--primary-color);
    }

    .btn-view:hover {
        background: rgba(30, 64, 175, 0.2);
        transform: translateY(-2px);
    }

    .btn-edit {
        background: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
    }

    .btn-edit:hover {
        background: rgba(139, 92, 246, 0.2);
        transform: translateY(-2px);
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .btn-delete:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: translateY(-2px);
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border: 2px solid #10b981;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        color: #065f46;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state-icon {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-state-text {
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .orders-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .stats-cards {
            grid-template-columns: 1fr;
        }

        .table-container {
            padding: 1rem;
            overflow-x: auto;
        }

        .device-specs {
            max-width: 150px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="orders-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-shopping-cart ms-2"></i>
                    طلبات الزبائن
                </h1>
                <p class="page-subtitle">إدارة ومتابعة طلبات العملاء</p>
            </div>
            <a href="{{ route('customer-orders.create') }}" class="btn-add-order">
                <i class="fas fa-plus"></i>
                <span>إضافة طلب جديد</span>
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle ms-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ $orders->where('status', 'pending')->count() }}</div>
            <div class="stat-label">قيد الانتظار</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon in-progress">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-value">{{ $orders->where('status', 'in_progress')->count() }}</div>
            <div class="stat-label">قيد التنفيذ</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $orders->where('status', 'completed')->count() }}</div>
            <div class="stat-label">مكتملة</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-value">{{ $orders->total() }}</div>
            <div class="stat-label">إجمالي الطلبات</div>
        </div>
    </div>

    <!-- Orders Table -->
    @if($orders->count() > 0)
        <div class="table-container">
            <div class="table-responsive">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>بيانات الزبون</th>
                            <th>نوع الجهاز</th>
                            <th>المواصفات</th>
                            <th>السعر التقريبي</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name">{{ $order->customer_name }}</div>
                                        <div class="customer-phone">
                                            <i class="fas fa-phone ms-1"></i>
                                            {{ $order->phone_number }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="device-type">{{ $order->device_type }}</div>
                                </td>
                                <td>
                                    <div class="device-specs" title="{{ $order->specifications }}">
                                        {{ Str::limit($order->specifications, 50) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="price-badge">{{ $order->approximate_price }}</span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $order->status }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('customer-orders.show', $order) }}" 
                                           class="btn-action btn-view" 
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customer-orders.edit', $order) }}" 
                                           class="btn-action btn-edit" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customer-orders.destroy', $order) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn-action btn-delete" 
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="empty-state-title">لا توجد طلبات حالياً</h3>
                <p class="empty-state-text">ابدأ بإضافة أول طلب للزبائن</p>
                <a href="{{ route('customer-orders.create') }}" class="btn-add-order">
                    <i class="fas fa-plus"></i>
                    <span>إضافة طلب جديد</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection