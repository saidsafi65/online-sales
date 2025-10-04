@extends('layout.app')

@section('title', 'التزامات المحل الشهرية')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="welcome-title">التزامات المحل الشهرية</h1>
        <p class="welcome-subtitle">عرض جميع التزامات المحل الشهرية مثل الرواتب والإيجار والمصروفات الشهرية</p>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Form -->
    <div class="filter-section mb-4">
        <form action="{{ route('obligations.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="expense_type" class="form-label">نوع التكاليف</label>
                <select name="expense_type" id="expense_type" class="form-select">
                    <option value="">جميع الأنواع</option>
                    <option value="salary" {{ request('expense_type') == 'salary' ? 'selected' : '' }}>الرواتب</option>
                    <option value="rent" {{ request('expense_type') == 'rent' ? 'selected' : '' }}>الإيجار</option>
                    <option value="exhibition_cost" {{ request('expense_type') == 'exhibition_cost' ? 'selected' : '' }}>
                        مصروفات المعرض</option>
                    <option value="electricity" {{ request('expense_type') == 'electricity' ? 'selected' : '' }}>الكهرباء
                    </option>
                    <option value="internet" {{ request('expense_type') == 'internet' ? 'selected' : '' }}>الإنترنت</option>
                </select>
            </div>

            <!-- تصفية حسب التاريخ الكامل -->
            <div class="col-md-3">
                <label for="date" class="form-label">التاريخ</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
            </div>

            <div class="col-md-3">
                <label for="payment_type" class="form-label">طريقة الدفع</label>
                <select name="payment_type" id="payment_type" class="form-select">
                    <option value="">جميع الطرق</option>
                    <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>نقدي</option>
                    <option value="bank" {{ request('payment_type') == 'bank' ? 'selected' : '' }}>بنكي</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> تصفية
                </button>
            </div>
        </form>
    </div>


    <!-- No Obligations Message -->
    @if ($obligations->isEmpty())
        <div class="alert alert-info">
            لا توجد التزامات حالياً.
        </div>
    @endif

    <!-- Add New Obligation Button -->
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('obligations.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> إضافة التزام جديد
        </a>
    </div>

    <!-- Obligations Grid -->
    <div class="row g-4 justify-content-center">
        @foreach ($obligations as $obligation)
            <div class="col-12 col-sm-6 col-lg-4">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>الخاصية</th>
                            <th>القيمة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>التزام شهر</td>
                            <td>{{ \Carbon\Carbon::parse($obligation->date)->format('F, Y') }}</td>
                        </tr>

                        <tr>
                            <td>نوع التكاليف</td>
                            <td>{{ ucfirst($obligation->expense_type) }}</td>
                        </tr>

                        @if ($obligation->cash_amount)
                            <tr>
                                <td>المبلغ النقدي</td>
                                <td>{{ $obligation->cash_amount }} شيكل</td>
                            </tr>
                        @endif

                        @if ($obligation->bank_amount)
                            <tr>
                                <td>المبلغ البنكي</td>
                                <td>{{ $obligation->bank_amount }} شيكل</td>
                            </tr>
                        @endif

                        <tr>
                            <td>طريقة الدفع</td>
                            <td>{{ ucfirst($obligation->payment_type) }}</td>
                        </tr>

                        <tr>
                            <td>التاريخ</td>
                            <td>{{ \Carbon\Carbon::parse($obligation->date)->format('d-m-Y') }}</td>
                        </tr>

                        @if ($obligation->detail)
                            <tr>
                                <td>تفاصيل إضافية</td>
                                <td>{{ $obligation->detail }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <!-- Pagination (if needed) -->
    <div class="d-flex justify-content-center mt-4">
        {{ $obligations->links() }}
    </div>
@endsection
