@extends('layout.app')

@section('title', 'التسليم اليومي')

@push('styles')
<style>
    .handover-header {
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
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-add {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
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

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .btn-reports {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
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

    .btn-reports:hover {
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

    .stat-icon.today {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #0ea5e9;
    }

    .stat-icon.month {
        background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
        color: #8b5cf6;
    }

    .stat-icon.total {
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

    .handovers-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .handovers-table thead tr {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
    }

    .handovers-table thead th {
        padding: 1rem;
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        text-align: center;
        border: none;
    }

    .handovers-table thead th:first-child {
        border-radius: 12px 0 0 0;
    }

    .handovers-table thead th:last-child {
        border-radius: 0 12px 0 0;
    }

    .handovers-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .handovers-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .handovers-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
        text-align: center;
    }

    .date-badge {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e3a8a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
    }

    .time-badge {
        background: var(--light-bg);
        color: var(--text-primary);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
    }

    .amount-badge {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        padding: 0.625rem 1.25rem;
        border-radius: 20px;
        font-weight: 900;
        font-size: 1.1rem;
        display: inline-block;
    }

    .reason-cell {
        text-align: right;
        font-weight: 600;
        color: var(--text-primary);
    }

    .action-buttons-table {
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
        .handover-header {
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

        .action-buttons {
            flex-direction: column;
        }

        .btn-add,
        .btn-reports {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="handover-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-hand-holding-usd ms-2"></i>
                    التسليم اليومي
                </h1>
                <p class="page-subtitle">إدارة ومتابعة التسليمات المالية اليومية</p>
            </div>
            <div class="action-buttons">
                <a href="{{ route('daily-handovers.reports') }}" class="btn-reports">
                    <i class="fas fa-chart-line"></i>
                    <span>التقارير المالية</span>
                </a>
                <a href="{{ route('daily-handovers.create') }}" class="btn-add">
                    <i class="fas fa-plus"></i>
                    <span>إضافة تسليم جديد</span>
                </a>
            </div>
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
            <div class="stat-icon today">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-value">{{ number_format($todayTotalCash + $todayTotalBank, 2) }} شيكل</div>
            <div class="stat-label">تسليمات اليوم</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon month">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-value">{{ number_format($monthTotalCash + $monthTotalBank, 2) }} شيكل</div>
            <div class="stat-label">تسليمات الشهر</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-value">{{ $handovers->total() }}</div>
            <div class="stat-label">إجمالي السجلات</div>
        </div>
    </div>

    <!-- Table -->
    @if($handovers->count() > 0)
        <div class="table-container">
            <div class="table-responsive">
                <table class="handovers-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>التاريخ</th>
                            <th>الساعة</th>
                            <th>الكاش</th>
                            <th>البنك</th>
                            <th>السبب</th>
                            <th>استلمه</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($handovers as $handover)
                            <tr>
                                <td>{{ $handover->id }}</td>
                                <td>
                                    <span class="date-badge">
                                        <i class="fas fa-calendar ms-1"></i>
                                        {{ $handover->handover_date->format('Y/m/d') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="time-badge">
                                        <i class="fas fa-clock ms-1"></i>
                                        {{ \Carbon\Carbon::parse($handover->handover_time)->format('h:i A') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="amount-badge">
                                        {{ number_format($handover->cash, 2) }} شيكل
                                    </span>
                                </td>
                                <td>
                                    <span class="amount-badge">
                                        {{ number_format($handover->bank, 2) }} شيكل
                                    </span>
                                </td>
                                <td class="reason-cell">{{ $handover->reason }}</td>
                                <td>{{ $handover->received_by ?? '-' }}</td>
                                <td>
                                    <div class="action-buttons-table">
                                        <a href="{{ route('daily-handovers.edit', $handover) }}" 
                                           class="btn-action btn-edit"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('daily-handovers.destroy', $handover) }}" 
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

            @if($handovers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $handovers->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <h3>لا توجد تسليمات مسجلة</h3>
                <p>ابدأ بإضافة أول تسليم يومي</p>
                <a href="{{ route('daily-handovers.create') }}" class="btn-add mt-3">
                    <i class="fas fa-plus"></i>
                    <span>إضافة تسليم جديد</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
