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

    <div class="alert {{ $totalDebts >= 0 ? 'alert-success' : 'alert-danger' }} d-flex justify-content-between align-items-center">
        <strong>صافي الديون (لنا − علينا، غير المسدَّدة فقط)</strong>
        <span class="fs-5 fw-bold">{{ number_format($totalDebts, 2) }} شيكل</span>
    </div>

    <x-filter-bar :action="route('debts.index')" :show-search="true" search-placeholder="ابحث بالاسم أو الجوال أو السبب...">
        <x-slot:extra>
            <div class="col-auto">
                <label class="form-label small mb-1">النوع</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">الكل</option>
                    <option value="دائن" {{ request('type') === 'دائن' ? 'selected' : '' }}>دائن (لي عنده)</option>
                    <option value="مدين" {{ request('type') === 'مدين' ? 'selected' : '' }}>مدين (عليّ له)</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">حالة السداد</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">الكل</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>غير مسدد</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>مسدد جزئياً</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>مسدد بالكامل</option>
                </select>
            </div>
        </x-slot:extra>
    </x-filter-bar>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم العميل</th>
                        <th>الجوال</th>
                        <th>النوع</th>
                        <th>الإجمالي</th>
                        <th>المسدد</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                        <th>تاريخ الدين</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debts as $debt)
                        @php
                            $statusColors = ['open' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
                        @endphp
                        <tr>
                            <td>{{ $debt->id }}</td>
                            <td>{{ $debt->customer_name }}</td>
                            <td>{{ $debt->phone }}</td>
                            <td>
                                <span class="badge {{ $debt->type == 'دائن' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $debt->type }}
                                </span>
                            </td>
                            <td><strong>{{ number_format($debt->total_amount, 2) }}</strong></td>
                            <td class="text-success">{{ number_format($debt->paid_amount, 2) }}</td>
                            <td class="text-danger">{{ number_format($debt->remaining_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$debt->payment_status] }}">
                                    {{ \App\Models\Debt::PAYMENT_STATUS_LABELS[$debt->payment_status] }}
                                </span>
                            </td>
                            <td>{{ $debt->debt_date->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('debts.show', $debt) }}" class="btn btn-sm btn-info text-white">عرض</a>
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