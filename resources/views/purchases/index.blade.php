@extends('layout.app')

@section('title', 'سجل المشتريات')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">سجل المشتريات</h3>
            <div class="btn-group">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary">إضافة عملية شراء</a>
                <a href="{{ route('purchases.create-catalog') }}" class="btn btn-success">إضافة + كتالوج</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select">
                            <option value="">الكل</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="app" {{ request('payment_method') == 'app' ? 'selected' : '' }}>تطبيق</option>
                            <option value="mixed" {{ request('payment_method') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="is_returned" class="form-select">
                            <option value="">الكل</option>
                            <option value="0" {{ request('is_returned') === '0' ? 'selected' : '' }}>غير مرجع</option>
                            <option value="1" {{ request('is_returned') === '1' ? 'selected' : '' }}>مرجع</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="ابحث بالصنف أو النوع أو اسم المورد أو رقم الجوال">
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button class="btn btn-secondary w-100">تصفية</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الصنف</th>
                                <th>النوع</th>
                                <th>الكمية</th>
                                <th>الدفع</th>
                                <th>مبلغ كاش</th>
                                <th>مبلغ بنك</th>
                                <th>الإجمالي</th>
                                <th>التاريخ</th>
                                <th>اسم المورد</th>
                                <th>رقم الجوال</th>
                                <th>صورة الهوية</th>
                                <th>مرجع</th>
                                <th>العطل</th>
                                <th>تاريخ الارجاع</th>
                                <th>ملاحظات</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->id }}</td>
                                    <td>{{ $purchase->item }}</td>
                                    <td>{{ $purchase->type }}</td>
                                    <td>{{ $purchase->quantity }}</td>
                                    <td><span class="badge bg-secondary">{{ $purchase->payment_method }}</span></td>
                                    <td>{{ number_format($purchase->amount_cash, 2) }}</td>
                                    <td>{{ number_format($purchase->amount_bank, 2) }}</td>
                                    <td><strong>{{ number_format($purchase->amount_cash + $purchase->amount_bank, 2) }}</strong></td>
                                    <td>{{ optional($purchase->purchase_date)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $purchase->supplier_name }}</td>
                                    <td>{{ $purchase->phone }}</td>
                                    <td>
                                        @if ($purchase->id_image)
                                            <a href="/{{ $purchase->id_image }}" target="_blank"
                                                class="btn btn-sm btn-outline-info">عرض</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($purchase->is_returned)
                                            <span class="badge bg-danger">مرجع</span>
                                        @else
                                            <span class="badge bg-success">غير مرجع</span>
                                        @endif
                                    </td>
                                    <td>{{ $purchase->issue ? Str::limit($purchase->issue, 25) : '-' }}</td>
                                    <td>{{ optional($purchase->return_date)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $purchase->notes ? Str::limit($purchase->notes, 25) : '-' }}</td>
                                    <td>
                                        <a href="{{ route('purchases.edit', $purchase) }}"
                                            class="btn btn-sm btn-outline-primary">تعديل</a>
                                        <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('تأكيد الحذف؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $purchases->links() }}
            </div>
        </div>
    </div>
@endsection