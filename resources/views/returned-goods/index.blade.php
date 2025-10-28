@extends('layout.app')

@section('title', 'البضاعة المرجعة')

@push('styles')
<style>
    .returns-header {
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
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    .btn-add-return {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
    }

    .btn-add-return:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
    }

    .stat-icon.pending {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #f59e0b;
    }

    .stat-icon.returned {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #ef4444;
    }

    .stat-icon.resolved {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #10b981;
    }

    .stat-icon.total {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #0ea5e9;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.95rem;
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

    .returns-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .returns-table thead tr {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .returns-table thead th {
        padding: 1rem;
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        text-align: center;
        border: none;
    }

    .returns-table thead th:first-child {
        border-radius: 12px 0 0 0;
    }

    .returns-table thead th:last-child {
        border-radius: 0 12px 0 0;
    }

    .returns-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .returns-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .returns-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        text-align: center;
    }

    .supplier-info {
        text-align: right;
    }

    .supplier-name {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .product-name {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .reason-cell {
        text-align: right;
        max-width: 250px;
    }

    .reason-text {
        color: var(--text-primary);
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .date-badge {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e3a8a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
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

    .status-badge.returned {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .status-badge.replaced {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-badge.refunded {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e3a8a;
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

    @media (max-width: 768px) {
        .returns-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-container {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="returns-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-undo-alt ms-2"></i>
                    البضاعة المرجعة
                </h1>
                <p class="page-subtitle">إدارة ومتابعة البضائع المرجعة للموردين</p>
            </div>
            <a href="{{ route('returned-goods.create') }}" class="btn-add-return">
                <i class="fas fa-plus"></i>
                <span>إضافة بضاعة مرجعة</span>
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

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ $pendingCount }}</div>
            <div class="stat-label">قيد الانتظار</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon returned">
                <i class="fas fa-undo"></i>
            </div>
            <div class="stat-value">{{ $returnedCount }}</div>
            <div class="stat-label">تم الإرجاع</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon resolved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $resolvedCount }}</div>
            <div class="stat-label">تم الحل</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-value">{{ $returnedGoods->total() }}</div>
            <div class="stat-label">إجمالي السجلات</div>
        </div>
    </div>

    <!-- Table -->
    @if($returnedGoods->count() > 0)
        <div class="table-container">
            <div class="table-responsive">
                <table class="returns-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المورد والمنتج</th>
                            <th>السبب</th>
                            <th>تاريخ الاكتشاف</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnedGoods as $return)
                            <tr>
                                <td>{{ $return->id }}</td>
                                <td>
                                    <div class="supplier-info">
                                        <div class="supplier-name">
                                            <i class="fas fa-truck ms-1"></i>
                                            {{ $return->supplier_name }}
                                        </div>
                                        <div class="product-name">
                                            <i class="fas fa-box ms-1"></i>
                                            {{ $return->product_name }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="reason-cell">
                                        <div class="reason-text" title="{{ $return->reason }}">
                                            {{ Str::limit($return->reason, 50) }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="date-badge">
                                        <i class="fas fa-calendar"></i>
                                        {{ $return->issue_discovered_date->format('Y/m/d') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $return->status }}">
                                        {{ $return->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('returned-goods.show', $return) }}" 
                                           class="btn-action btn-view"
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('returned-goods.edit', $return) }}" 
                                           class="btn-action btn-edit"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('returned-goods.destroy', $return) }}" 
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="حذف">
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

            @if($returnedGoods->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $returnedGoods->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <h3>لا توجد بضائع مرجعة</h3>
                <p>لم يتم تسجيل أي بضاعة مرجعة حتى الآن</p>
                <a href="{{ route('returned-goods.create') }}" class="btn-add-return mt-3">
                    <i class="fas fa-plus"></i>
                    <span>إضافة بضاعة مرجعة</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection