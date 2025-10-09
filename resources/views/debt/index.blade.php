@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إدارة الديون</h2>
        <a href="{{ route('debts.create') }}" class="btn btn-primary">إضافة دين جديد</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
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
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debts as $debt)
                        <tr>
                            <td>{{ $debt->id }}</td>
                            <td>{{ $debt->customer_name }}</td>
                            <td>{{ $debt->phone }}</td>
                            <td>
                                <span class="badge {{ $debt->type == 'دائن' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $debt->type }}
                                </span>
                            </td>
                            <td>{{ number_format($debt->cash_amount, 2) }}</td>
                            <td>{{ number_format($debt->bank_amount, 2) }}</td>
                            <td><strong>{{ number_format($debt->total_amount, 2) }}</strong></td>
                            <td>{{ $debt->debt_date->format('Y-m-d') }}</td>
                            <td>{{ $debt->payment_date ? $debt->payment_date->format('Y-m-d') : '-' }}</td>
                            <td>
                                <a href="{{ route('debts.edit', $debt) }}" class="btn btn-sm btn-warning">تعديل</a>
                                <form action="{{ route('debts.destroy', $debt) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">لا توجد سجلات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $debts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection