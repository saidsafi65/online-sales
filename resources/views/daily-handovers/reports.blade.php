@extends('layout.app')

@section('title', 'التقارير المالية')

@push('styles')
<style>
    .reports-header {
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
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .filter-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
    }

    .btn-filter {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .summary-icon {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }

    .summary-icon.sales {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #10b981;
    }

    .summary-icon.handovers {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #0ea5e9;
    }

    .summary-icon.difference {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #f59e0b;
    }

    .summary-icon.difference.positive {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #10b981;
    }

    .summary-icon.difference.negative {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #ef4444;
    }

    .summary-label {
        font-size: 1rem;
        color: var(--text-secondary);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .summary-value {
        font-size: 2.25rem;
        font-weight: 900;
        color: var(--text-primary);
    }

    .summary-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 0.75rem;
    }

    .badge-positive {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .badge-negative {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .daily-report-table {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-lg);
        border: 2px solid var(--border-color);
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .reports-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .reports-table thead tr {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
    }

    .reports-table thead th {
        padding: 1rem;
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        text-align: center;
        border: none;
    }

    .reports-table thead th:first-child {
        border-radius: 12px 0 0 0;
    }

    .reports-table thead th:last-child {
        border-radius: 0 12px 0 0;
    }

    .reports-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .reports-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
    }

    .reports-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        text-align: center;
        vertical-align: middle;
    }

    .date-cell {
        font-weight: 600;
        color: var(--text-primary);
    }

    .amount-cell {
        font-weight: 700;
        font-size: 1.05rem;
    }

    .amount-sales {
        color: #10b981;
    }

    .amount-handover {
        color: #0ea5e9;
    }

    .amount-positive {
        color: #10b981;
    }

    .amount-negative {
        color: #ef4444;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .daily-report-table {
            overflow-x: auto;
        }

        .summary-value {
            font-size: 1.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="reports-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-chart-line ms-2"></i>
                    التقارير المالية
                </h1>
                <p class="page-subtitle">مقارنة الإيرادات مع التسليمات اليومية</p>
            </div>
            <a href="{{ route('daily-handovers.index') }}" class="btn-filter">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للتسليمات</span>
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <h3 class="filter-title">
            <i class="fas fa-filter"></i>
            تصفية حسب التاريخ
        </h3>
        <form method="GET" action="{{ route('daily-handovers.reports') }}">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" 
                           name="start_date" 
                           class="form-control" 
                           value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" 
                           name="end_date" 
                           class="form-control" 
                           value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn-filter w-100">
                        <i class="fas fa-search"></i>
                        <span>عرض التقرير</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-icon sales">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="summary-label">إجمالي الإيرادات (المبيعات)</div>
            <div class="summary-value">{{ number_format($totalSales, 2) }} شيكل</div>
        </div>

        <div class="summary-card">
            <div class="summary-icon handovers">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="summary-label">إجمالي التسليمات</div>
            <div class="summary-value">{{ number_format($totalHandovers, 2) }} شيكل</div>
        </div>

        <div class="summary-card">
            <div class="summary-icon difference {{ $difference >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-{{ $difference >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
            </div>
            <div class="summary-label">الفرق</div>
            <div class="summary-value">{{ number_format(abs($difference), 2) }} شيكل</div>
            <span class="summary-badge {{ $difference >= 0 ? 'badge-positive' : 'badge-negative' }}">
                @if($difference > 0)
                    <i class="fas fa-check-circle ms-1"></i>
                    فائض في الإيرادات
                @elseif($difference < 0)
                    <i class="fas fa-exclamation-triangle ms-1"></i>
                    عجز في التسليمات
                @else
                    <i class="fas fa-equals ms-1"></i>
                    متطابق
                @endif
            </span>
        </div>
    </div>

    <!-- Daily Report Table -->
    <div class="daily-report-table">
        <h3 class="table-title">
            <i class="fas fa-calendar-alt"></i>
            التقرير اليومي التفصيلي
        </h3>

        @if($dailyData->count() > 0)
            <div class="table-responsive">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الإيرادات</th>
                            <th>التسليمات</th>
                            <th>الفرق</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyData as $data)
                            <tr>
                                <td class="date-cell">
                                    {{ \Carbon\Carbon::parse($data['date'])->format('Y/m/d') }}
                                </td>
                                <td class="amount-cell amount-sales">
                                    {{ number_format($data['sales'], 2) }} شيكل
                                </td>
                                <td class="amount-cell amount-handover">
                                    {{ number_format($data['handover'], 2) }} شيكل
                                </td>
                                <td class="amount-cell {{ $data['difference'] >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                    {{ number_format(abs($data['difference']), 2) }} شيكل
                                </td>
                                <td>
                                    @if($data['difference'] > 0)
                                        <span class="summary-badge badge-positive">
                                            <i class="fas fa-check-circle ms-1"></i>
                                            فائض
                                        </span>
                                    @elseif($data['difference'] < 0)
                                        <span class="summary-badge badge-negative">
                                            <i class="fas fa-exclamation-triangle ms-1"></i>
                                            عجز
                                        </span>
                                    @else
                                        <span class="summary-badge" style="background: #e2e8f0; color: #64748b;">
                                            <i class="fas fa-equals ms-1"></i>
                                            متطابق
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>لا توجد بيانات للفترة المحددة</h3>
                <p>جرب تغيير الفترة الزمنية للتقرير</p>
            </div>
        @endif
    </div>
</div>
@endsection