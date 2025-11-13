@extends('layout.app')

@section('title', 'المصروفات')

@section('content')
    <div class="container-fluid">
        <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>المصروفات</h3>
            <a href="{{ route('mobile-shop.expenses.create') }}" class="btn btn-primary">إضافة مصروف</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div style="overflow-x:auto;">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصنف</th>
                            <th>النوع</th>
                            <th>الكمية</th>
                            <th>طريقة الدفع</th>
                            <th>كاش</th>
                            <th>بنك</th>
                            <th>الإجمالي</th>
                            <th>التاريخ</th>
                            <th>المورد</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr>
                                <td>{{ $loop->iteration + ($expenses->currentPage()-1)*$expenses->perPage() }}</td>
                                <td>{{ $e->category }}</td>
                                <td>{{ $e->type }}</td>
                                <td>{{ $e->quantity }}</td>
                                <td>{{ $e->payment_method }}</td>
                                <td>{{ number_format($e->cash_amount, 2) }}</td>
                                <td>{{ number_format($e->bank_amount, 2) }}</td>
                                <td>{{ number_format($e->total, 2) }}</td>
                                <td>{{ $e->expense_date }}</td>
                                <td>{{ $e->supplier_name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('mobile-shop.expenses.edit', $e) }}" class="btn btn-sm btn-primary">تعديل</a>
                                    <form action="{{ route('mobile-shop.expenses.destroy', $e) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center">لا توجد مصروفات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $expenses->links() }}</div>
        </div>
    </div>
@endsection
