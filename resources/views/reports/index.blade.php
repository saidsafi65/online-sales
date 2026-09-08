@extends('layout.app')

@section('title', 'التقارير')

@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">التقارير</h1>
            <a href="{{ route('reports.export-pdf', request()->query()) }}" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-2"></i>تصدير PDF
            </a>
        </div>

        <x-filter-bar :action="route('reports.index')">
            <x-slot:extra>
                <div class="col-auto">
                    <label class="form-label small mb-1">النوع</label>
                    <select id="type" name="type" class="form-select form-select-sm">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="sales" {{ $type === 'sales' ? 'selected' : '' }}>مبيعات</option>
                        <option value="repairs" {{ $type === 'repairs' ? 'selected' : '' }}>صيانات</option>
                        <option value="purchases" {{ $type === 'purchases' ? 'selected' : '' }}>مشتريات</option>
                    </select>
                </div>
            </x-slot:extra>
        </x-filter-bar>

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

            @if ($type === 'all')
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title">مبيعات المتجر الإلكتروني (أونلاين)</div>
                            <div class="display-6">{{ number_format((float) ($onlineOrdersTotal ?? 0), 2) }}</div>
                            <div class="text-muted small mt-2">
                                عدد الطلبات المكتملة: {{ (int) ($onlineOrdersCount ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title">الالتزامات الشهرية (رواتب، إيجار...)</div>
                            <div class="display-6">{{ number_format((float) ($monthlyObligations ?? 0), 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 {{ ($netIncome ?? 0) >= 0 ? 'border-success' : 'border-danger' }}">
                        <div class="card-body">
                            <div class="card-title">صافي الدخل (مبيعات + صيانة + أونلاين − مشتريات − التزامات)</div>
                            <div class="display-6 {{ ($netIncome ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format((float) ($netIncome ?? 0), 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- تفصيل حسب طريقة الدفع (مبيعات) -->
        @if (in_array($type, ['all', 'sales']) && !empty($salesByPaymentMethod))
            <div class="mb-4">
                <h5 class="mb-2">تفصيل المبيعات حسب طريقة الدفع</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>طريقة الدفع</th>
                                <th>عدد العمليات</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesByPaymentMethod as $method => $row)
                                <tr>
                                    <td>{{ $method }}</td>
                                    <td>{{ $row['count'] }}</td>
                                    <td class="fw-bold">{{ number_format($row['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- تفصيل حسب نوع الجهاز (صيانة) -->
        @if (in_array($type, ['all', 'repairs']) && !empty($repairsByDevice))
            <div class="mb-4">
                <h5 class="mb-2">الأكثر صيانة (حسب نوع الجهاز)</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>الجهاز</th>
                                <th>عدد الصيانات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($repairsByDevice as $row)
                                <tr>
                                    <td>{{ $row['device'] }}</td>
                                    <td class="fw-bold">{{ $row['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- أكثر الموردين (مشتريات) -->
        @if (in_array($type, ['all', 'purchases']) && !empty($topSuppliers))
            <div class="mb-4">
                <h5 class="mb-2">أكثر الموردين تعاملاً</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>المورد</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topSuppliers as $row)
                                <tr>
                                    <td>{{ $row['supplier'] }}</td>
                                    <td class="fw-bold">{{ number_format($row['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

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
                                        <td>{{ $branchNames[$branchId] ?? '#'.$branchId }}</td>
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
                                        <td>{{ $branchNames[$branchId] ?? '#'.$branchId }}</td>
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
                                        <td>{{ $branchNames[$branchId] ?? '#'.$branchId }}</td>
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
