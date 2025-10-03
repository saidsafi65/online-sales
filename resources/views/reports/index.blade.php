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
            @if(request('date'))
                لـ {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
            @endif
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>نوع التقرير</th>
                        <th>القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    @if(in_array(request('type', 'all'), ['all', 'sales']))
                    <tr>
                        <td>إجمالي المبيعات</td>
                        <td>{{ number_format($totalSales ?? 0, 2) }} ₪</td>
                    </tr>
                    @endif

                    @if(in_array(request('type', 'all'), ['all', 'repairs']))
                    <tr>
                        <td>إجمالي الصيانات</td>
                        <td>{{ $totalRepairs ?? 0 }}</td>
                    </tr>
                    @endif

                    @if(in_array(request('type', 'all'), ['all', 'purchases']))
                    <tr>
                        <td>إجمالي المشتريات</td>
                        <td>{{ number_format($totalPurchases ?? 0, 2) }} ₪</td>
                    </tr>
                    @endif

                    @if(in_array(request('type', 'all'), ['all', 'repairs']))
                    <tr>
                        <td>إجمالي العملاء</td>
                        <td>{{ $totalCustomers ?? 0 }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
