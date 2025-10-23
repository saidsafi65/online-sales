@extends('layout.app')

@section('content')
    <div class="container py-3">
        <h1 class="mb-3">التقارير</h1>

        @php
            // تطبيع القيم القادمة من الكنترولر
            $dateFromVal = now()->format('Y-m-d');
            $dateToVal = now()->format('Y-m-d');
        @endphp

        <!-- نموذج التصفية من تاريخ إلى تاريخ -->
        <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end mb-4">
            <div class="col-auto">
                <label for="date_from" class="form-label">من تاريخ</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from', $dateFromVal) }}"
                    class="form-control" required>
            </div>
            <div class="col-auto">
                <label for="date_to" class="form-label">إلى تاريخ</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to', $dateToVal) }}"
                    class="form-control" required>
            </div>
            <div class="col-auto">
                <label for="type" class="form-label">النوع</label>
                <select id="type" name="type" class="form-select">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="sales" {{ $type === 'sales' ? 'selected' : '' }}>مبيعات</option>
                    <option value="repairs" {{ $type === 'repairs' ? 'selected' : '' }}>صيانات</option>
                    <option value="purchases" {{ $type === 'purchases' ? 'selected' : '' }}>مشتريات</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">تصفية</button>
            </div>
        </form>

        <!-- ملخص الإجماليات -->
        <div class="row g-3 mb-4">
            @if (in_array($type, ['all', 'sales']))
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title">إجمالي المبيعات</div>
                            <div class="display-6">{{ number_format((float) ($monthlySales ?? 0), 2) }}</div>
                            <div class="text-muted small mt-2">
                                نقداً: {{ number_format((float) ($monthlySalesCash ?? 0), 2) }} • تطبيق:
                                {{ number_format((float) ($monthlySalesAppAmount ?? 0), 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array($type, ['all', 'repairs']))
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title">إجمالي تكاليف الصيانة</div>
                            <div class="display-6">{{ number_format((float) ($monthlycostRepair ?? 0), 2) }}</div>
                            <div class="text-muted small mt-2">
                                نقداً: {{ number_format((float) ($monthlycost_cashRepair ?? 0), 2) }} • بنك:
                                {{ number_format((float) ($monthlycost_bankRepair ?? 0), 2) }}
                            </div>
                            <div class="text-muted small mt-2">
                                عدد الصيانات المستلمة: {{ (int) ($totalRepairs ?? 0) }} • العملاء:
                                {{ (int) ($totalCustomers ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array($type, ['all', 'purchases']))
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title">إجمالي المشتريات</div>
                            <div class="display-6">{{ number_format((float) ($monthlyPurchases ?? 0), 2) }}</div>
                            <div class="text-muted small mt-2">
                                نقداً: {{ number_format((float) ($cashTotal ?? 0), 2) }} • بنك:
                                {{ number_format((float) ($bankTotal ?? 0), 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- تفصيل حسب الفروع - يظهر للـ Admin فقط -->
        @if (!empty($isAdmin) && $isAdmin)
            @if (in_array($type, ['all', 'sales']) && !empty($salesByBranch))
                <div class="mb-4">
                    <h5 class="mb-2">تفصيل المبيعات حسب الفروع</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>الفرع</th>
                                    <th>نقداً</th>
                                    <th>تطبيق</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesByBranch as $branchId => $row)
                                    <tr>
                                        <td>#{{ $branchId }}</td>
                                        <td>{{ number_format((float) ($row['cash'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($row['app'] ?? 0), 2) }}</td>
                                        <td class="fw-bold">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if (in_array($type, ['all', 'repairs']) && !empty($repairsCostByBranch))
                <div class="mb-4">
                    <h5 class="mb-2">تفصيل تكاليف الصيانة حسب الفروع</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>الفرع</th>
                                    <th>نقداً</th>
                                    <th>بنك</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($repairsCostByBranch as $branchId => $row)
                                    <tr>
                                        <td>#{{ $branchId }}</td>
                                        <td>{{ number_format((float) ($row['cash'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($row['bank'] ?? 0), 2) }}</td>
                                        <td class="fw-bold">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if (in_array($type, ['all', 'purchases']) && !empty($purchasesByBranch))
                <div class="mb-4">
                    <h5 class="mb-2">تفصيل المشتريات حسب الفروع</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>الفرع</th>
                                    <th>نقداً</th>
                                    <th>بنك</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchasesByBranch as $branchId => $row)
                                    <tr>
                                        <td>#{{ $branchId }}</td>
                                        <td>{{ number_format((float) ($row['cash'] ?? 0), 2) }}</td>
                                        <td>{{ number_format((float) ($row['bank'] ?? 0), 2) }}</td>
                                        <td class="fw-bold">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection

