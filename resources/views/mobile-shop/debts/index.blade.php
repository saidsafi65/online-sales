@extends('layout.app')

@section('title', 'الديون')

@section('content')
    <div class="container-fluid">
        <a href="{{ route('mobile-shop.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>الديون</h3>
            <a href="{{ route('mobile-shop.debts.create') }}" class="btn btn-primary">إضافة دين</a>
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
                            <th>اسم العميل</th>
                            <th>الجوال</th>
                            <th>النوع</th>
                            <th>نقدي</th>
                            <th>بنكي</th>
                            <th>الإجمالي</th>
                            <th>تاريخ الدين</th>
                            <th>تاريخ السداد</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $d)
                            <tr>
                                <td>{{ $loop->iteration + ($debts->currentPage()-1)*$debts->perPage() }}</td>
                                <td>{{ $d->customer_name }}</td>
                                <td>{{ $d->phone_number }}</td>
                                <td>{{ $d->type }}</td>
                                <td>{{ number_format($d->cash_amount, 2) }}</td>
                                <td>{{ number_format($d->bank_amount, 2) }}</td>
                                <td>{{ number_format($d->total, 2) }}</td>
                                <td>{{ $d->debt_date }}</td>
                                <td>{{ $d->payment_date ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('mobile-shop.debts.edit', $d) }}" class="btn btn-sm btn-primary">تعديل</a>
                                    <form action="{{ route('mobile-shop.debts.destroy', $d) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">لا توجد ديون</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $debts->links() }}
            </div>
        </div>
    </div>
@endsection
