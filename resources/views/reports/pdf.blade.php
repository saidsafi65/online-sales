<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<style>
    @font-face {
        font-family: 'Amiri';
        font-style: normal;
        font-weight: normal;
        src: url('{{ resource_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
    }
    @font-face {
        font-family: 'Amiri';
        font-style: normal;
        font-weight: bold;
        src: url('{{ resource_path('fonts/Amiri-Bold.ttf') }}') format('truetype');
    }

    * {
        font-family: 'Amiri', sans-serif;
    }

    body {
        direction: rtl;
        text-align: right;
        font-size: 13px;
        color: #1f2937;
    }

    .header {
        display: flex;
        border-bottom: 3px solid {{ $__pdfPrimaryColor ?? '#dc2626' }};
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .header table { width: 100%; border-collapse: collapse; }
    .header td { vertical-align: middle; }
    .header .logo img { max-height: 60px; max-width: 120px; }
    .header .store-name { font-size: 20px; font-weight: bold; color: {{ $__pdfPrimaryColor ?? '#dc2626' }}; }
    .header .report-meta { text-align: left; font-size: 12px; color: #6b7280; }

    h2.report-title { font-size: 17px; margin: 10px 0; }

    .summary-cards { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .summary-cards td { padding: 8px; }
    .summary-card {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 10px;
    }
    .summary-card .label { font-size: 11px; color: #6b7280; margin-bottom: 4px; }
    .summary-card .value { font-size: 18px; font-weight: bold; }
    .summary-card .detail { font-size: 10px; color: #6b7280; margin-top: 4px; }
    .net-positive { color: #059669; }
    .net-negative { color: #dc2626; }

    h5.section-title {
        font-size: 14px;
        margin: 14px 0 6px;
        border-right: 4px solid {{ $__pdfPrimaryColor ?? '#dc2626' }};
        padding-right: 8px;
    }

    table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.data-table th, table.data-table td {
        border: 1px solid #e5e7eb;
        padding: 5px 8px;
        font-size: 11px;
        text-align: right;
    }
    table.data-table th { background: #f3f4f6; font-weight: bold; }

    .footer-note { font-size: 9px; color: #9ca3af; margin-top: 20px; text-align: center; }
</style>
</head>
<body>
@php
    $__pdfTenant = app()->bound('currentTenant') ? app('currentTenant') : null;
    $__pdfPrimaryColor = $__pdfTenant->brand_primary_color ?? '#dc2626';
    $__pdfLogoPath = null;
    if ($__pdfTenant && $__pdfTenant->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($__pdfTenant->logo_path)) {
        $__pdfLogoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($__pdfTenant->logo_path);
    }
@endphp

<div class="header">
    <table>
        <tr>
            <td style="width: 20%;" class="logo">
                @if ($__pdfLogoPath)
                    <img src="{{ $__pdfLogoPath }}" alt="{{ $__pdfTenant->name ?? '' }}">
                @endif
            </td>
            <td style="width: 50%;">
                <div class="store-name">{{ $__pdfTenant->name ?? 'Online Sale' }}</div>
            </td>
            <td style="width: 30%;" class="report-meta">
                تاريخ الإصدار: {{ now()->format('Y-m-d H:i') }}<br>
                الفترة: {{ $dateStart->format('Y-m-d') }} — {{ $dateEnd->format('Y-m-d') }}
            </td>
        </tr>
    </table>
</div>

<h2 class="report-title">التقرير المالي</h2>

<table class="summary-cards">
    <tr>
        @if (in_array($type, ['all', 'sales']))
        <td style="width: 33%;">
            <div class="summary-card">
                <div class="label">إجمالي المبيعات</div>
                <div class="value">{{ number_format((float) ($monthlySales ?? 0), 2) }}</div>
                <div class="detail">نقداً: {{ number_format((float) ($monthlySalesCash ?? 0), 2) }} • تطبيق: {{ number_format((float) ($monthlySalesAppAmount ?? 0), 2) }}</div>
            </div>
        </td>
        @endif
        @if (in_array($type, ['all', 'repairs']))
        <td style="width: 33%;">
            <div class="summary-card">
                <div class="label">إجمالي تكاليف الصيانة</div>
                <div class="value">{{ number_format((float) ($monthlycostRepair ?? 0), 2) }}</div>
                <div class="detail">نقداً: {{ number_format((float) ($monthlycost_cashRepair ?? 0), 2) }} • بنك: {{ number_format((float) ($monthlycost_bankRepair ?? 0), 2) }}</div>
                <div class="detail">عدد الصيانات: {{ (int) ($totalRepairs ?? 0) }} • العملاء: {{ (int) ($totalCustomers ?? 0) }}</div>
            </div>
        </td>
        @endif
        @if (in_array($type, ['all', 'purchases']))
        <td style="width: 33%;">
            <div class="summary-card">
                <div class="label">إجمالي المشتريات</div>
                <div class="value">{{ number_format((float) ($monthlyPurchases ?? 0), 2) }}</div>
                <div class="detail">نقداً: {{ number_format((float) ($cashTotal ?? 0), 2) }} • بنك: {{ number_format((float) ($bankTotal ?? 0), 2) }}</div>
            </div>
        </td>
        @endif
    </tr>
    @if ($type === 'all')
    <tr>
        <td style="width: 33%;">
            <div class="summary-card">
                <div class="label">الالتزامات الشهرية</div>
                <div class="value">{{ number_format((float) ($monthlyObligations ?? 0), 2) }}</div>
            </div>
        </td>
        <td style="width: 33%;">
            <div class="summary-card">
                <div class="label">صافي الدخل</div>
                <div class="value {{ ($netIncome ?? 0) >= 0 ? 'net-positive' : 'net-negative' }}">{{ number_format((float) ($netIncome ?? 0), 2) }}</div>
            </div>
        </td>
    </tr>
    @endif
</table>

@if (in_array($type, ['all', 'sales']) && !empty($salesByPaymentMethod))
    <h5 class="section-title">تفصيل المبيعات حسب طريقة الدفع</h5>
    <table class="data-table">
        <thead><tr><th>طريقة الدفع</th><th>عدد العمليات</th><th>الإجمالي</th></tr></thead>
        <tbody>
        @foreach ($salesByPaymentMethod as $method => $row)
            <tr><td>{{ $method }}</td><td>{{ $row['count'] }}</td><td>{{ number_format($row['total'], 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
@endif

@if (in_array($type, ['all', 'repairs']) && !empty($repairsByDevice))
    <h5 class="section-title">الأكثر صيانة (حسب نوع الجهاز)</h5>
    <table class="data-table">
        <thead><tr><th>الجهاز</th><th>عدد الصيانات</th></tr></thead>
        <tbody>
        @foreach ($repairsByDevice as $row)
            <tr><td>{{ $row['device'] }}</td><td>{{ $row['count'] }}</td></tr>
        @endforeach
        </tbody>
    </table>
@endif

@if (in_array($type, ['all', 'purchases']) && !empty($topSuppliers))
    <h5 class="section-title">أكثر الموردين تعاملاً</h5>
    <table class="data-table">
        <thead><tr><th>المورد</th><th>الإجمالي</th></tr></thead>
        <tbody>
        @foreach ($topSuppliers as $row)
            <tr><td>{{ $row['supplier'] }}</td><td>{{ number_format($row['total'], 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
@endif

@if (!empty($isAdmin) && $isAdmin)
    @if (in_array($type, ['all', 'sales']) && !empty($salesByBranch))
        <h5 class="section-title">تفصيل المبيعات حسب الفروع</h5>
        <table class="data-table">
            <thead><tr><th>الفرع</th><th>نقداً</th><th>تطبيق</th><th>الإجمالي</th></tr></thead>
            <tbody>
            @foreach ($salesByBranch as $branchId => $row)
                <tr>
                    <td>{{ $branchNames[$branchId] ?? '#'.$branchId }}</td>
                    <td>{{ number_format((float) ($row['cash'] ?? 0), 2) }}</td>
                    <td>{{ number_format((float) ($row['app'] ?? 0), 2) }}</td>
                    <td>{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if (in_array($type, ['all', 'repairs']) && !empty($repairsCostByBranch))
        <h5 class="section-title">تفصيل تكاليف الصيانة حسب الفروع</h5>
        <table class="data-table">
            <thead><tr><th>الفرع</th><th>نقداً</th><th>بنك</th><th>الإجمالي</th></tr></thead>
            <tbody>
            @foreach ($repairsCostByBranch as $branchId => $row)
                <tr>
                    <td>{{ $branchNames[$branchId] ?? '#'.$branchId }}</td>
                    <td>{{ number_format((float) ($row['cash'] ?? 0), 2) }}</td>
                    <td>{{ number_format((float) ($row['bank'] ?? 0), 2) }}</td>
                    <td>{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if (in_array($type, ['all', 'purchases']) && !empty($purchasesByBranch))
        <h5 class="section-title">تفصيل المشتريات حسب الفروع</h5>
        <table class="data-table">
            <thead><tr><th>الفرع</th><th>نقداً</th><th>بنك</th><th>الإجمالي</th></tr></thead>
            <tbody>
            @foreach ($purchasesByBranch as $branchId => $row)
                <tr>
                    <td>{{ $branchNames[$branchId] ?? '#'.$branchId }}</td>
                    <td>{{ number_format((float) ($row['cash'] ?? 0), 2) }}</td>
                    <td>{{ number_format((float) ($row['bank'] ?? 0), 2) }}</td>
                    <td>{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endif

<div class="footer-note">تم إنشاء هذا التقرير تلقائياً بواسطة نظام {{ $__pdfTenant->name ?? 'Online Sale' }}</div>
</body>
</html>
