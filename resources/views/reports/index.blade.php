@extends('layout.app')

@section('title', 'التقارير')

@section('content')
    <div class="container py-4">

        <h1 class="mb-4">التقارير والإحصائيات</h1>

        <p>مرحباً بك في صفحة التقارير. هنا يمكنك عرض وتحليل بيانات المبيعات، الصيانة، والمشتريات.</p>

        <!-- نموذج البحث -->
        <form method="GET" action="{{ route('reports.index') }}" class="mb-4 row g-3 align-items-end">
            <div class="col-auto">
                <label for="date" class="form-label">اختر التاريخ</label>
                <input type="date" id="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>

            <div class="col-auto">
                <label for="type" class="form-label">نوع التقرير</label>
                <select id="type" name="type" class="form-select">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>الكل</option>
                    <option value="sales" {{ request('type') == 'sales' ? 'selected' : '' }}>المبيعات</option>
                    <option value="repairs" {{ request('type') == 'repairs' ? 'selected' : '' }}>الصيانة</option>
                    <option value="purchases" {{ request('type') == 'purchases' ? 'selected' : '' }}>المشتريات</option>
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary">بحث</button>
            </div>
        </form>

        <!-- جدول التقرير -->
        <div class="card mt-4">
            <div class="card-header">
                ملخص التقرير
                @if (request('date'))
                    لـ {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                @endif
            </div>
            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>نوع التقرير</th>
                            <th>القيمة نقدي</th>
                            <th>القيمة بنكي</th>
                            <th>القيمة الإجمالية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (in_array(request('type', 'all'), ['all', 'sales']))
                            <tr>
                                <td>إجمالي المبيعات</td>
                                <td>نقدي {{ number_format($monthlySalesCash ?? 0, 2) }} شيكل</td>
                                <td>بنكي {{ number_format($monthlySalesAppAmount ?? 0, 2) }} شيكل</td>
                                <td>المبلغ الكلي {{ number_format($monthlySalesCash + $monthlySalesAppAmount ?? 0, 2) }}
                                    شيكل
                                </td>
                            </tr>
                        @endif

                        @if (in_array(request('type', 'all'), ['all', 'repairs']))
                            <tr>
                                <td>إجمالي عدد الصيانات</td>
                                <td colspan="3">{{ $totalRepairs ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>اجمالي نقود الصيانة</td>
                                <td>نقدي {{ number_format($monthlycost_cashRepair ?? 0, 2) }} شيكل</td>
                                <td>بنكي {{ number_format($monthlycost_bankRepair ?? 0, 2) }} شيكل</td>
                                <td>المبلغ الكلي {{ number_format($monthlycostRepair ?? 0, 2) }} شيكل</td>
                            </tr>
                        @endif

                        @if (in_array(request('type', 'all'), ['all', 'purchases']))
                            <tr>
                                <td>إجمالي المشتريات</td>
                                <td>نقدي {{ number_format($cashTotal ?? 0, 2) }} شيكل</td>
                                <td>بنكي {{ number_format($bankTotal ?? 0, 2) }} شيكل</td>
                                <td>المبلغ الكلي {{ number_format($cashTotal + $bankTotal ?? 0, 2) }} شيكل</td>
                            </tr>
                        @endif

                        @if (in_array(request('type', 'all'), ['all', 'repairs']))
                            <tr>
                                <td>إجمالي العملاء</td>
                                <td colspan="3">{{ $totalCustomers ?? 0 }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
