@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل الدين #{{ $debt->id }}</h2>
        <div class="d-flex gap-2">
            @if ($debt->type === 'دائن' && $debt->remaining_amount > 0)
                <form action="{{ route('debts.send-reminder', $debt) }}" method="POST" onsubmit="return confirm('إرسال تذكير SMS بالمبلغ المتبقي؟')">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-comment-sms me-1"></i> تذكير SMS
                    </button>
                </form>
            @endif
            <a href="{{ route('debts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i> رجوع للقائمة
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($debt->wholesaleInvoice)
        <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span><i class="fas fa-link me-1"></i> هاي الدين جاي من فاتورة بيع بالجملة رقم {{ $debt->wholesaleInvoice->invoice_number }} — الدفعات بتتسجل من صفحة الفاتورة نفسها.</span>
            <a href="{{ route('wholesale-invoices.show', $debt->wholesaleInvoice->id) }}" class="btn btn-sm btn-info text-white">عرض الفاتورة</a>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">بيانات الدين</h5>
            @php
                $statusColors = [
                    'open' => 'danger',
                    'partial' => 'warning',
                    'paid' => 'success',
                ];
            @endphp
            <span class="badge bg-{{ $statusColors[$debt->payment_status] }}">
                {{ \App\Models\Debt::PAYMENT_STATUS_LABELS[$debt->payment_status] }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">الاسم</div>
                    <div class="fw-bold">{{ $debt->customer_name }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">الجوال</div>
                    <div class="fw-bold">{{ $debt->phone }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">النوع</div>
                    <div>
                        <span class="badge {{ $debt->type == 'دائن' ? 'bg-success' : 'bg-danger' }}">{{ $debt->type }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">تاريخ الدين</div>
                    <div class="fw-bold">{{ $debt->debt_date->format('Y-m-d') }}</div>
                </div>
                <div class="col-md-8">
                    <div class="text-muted small">السبب</div>
                    <div>{{ $debt->reason }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">الدفعات</h5></div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="bg-light rounded p-3 text-center">
                        <div class="text-muted small">المبلغ الأصلي</div>
                        <div class="fs-5 fw-bold">{{ number_format($debt->total_amount, 2) }} شيكل</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded p-3 text-center" style="background:#f0fdf4 !important;">
                        <div class="text-success small">المسدد</div>
                        <div class="fs-5 fw-bold text-success">{{ number_format($debt->paid_amount, 2) }} شيكل</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded p-3 text-center" style="background:#fef2f2 !important;">
                        <div class="text-danger small">المتبقي</div>
                        <div class="fs-5 fw-bold text-danger">{{ number_format($debt->remaining_amount, 2) }} شيكل</div>
                    </div>
                </div>
            </div>

            @if ($debt->payments->isNotEmpty())
                <div class="table-responsive mb-3">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>نقدي</th>
                                <th>بنكي</th>
                                <th>المبلغ</th>
                                <th>استلمها</th>
                                <th>ملاحظات</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($debt->payments->sortByDesc('payment_date') as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td>{{ number_format($payment->cash_amount, 2) }}</td>
                                    <td>{{ number_format($payment->bank_amount, 2) }}</td>
                                    <td class="fw-bold">{{ number_format($payment->total_amount, 2) }}</td>
                                    <td>{{ $payment->received_by }}</td>
                                    <td>{{ $payment->notes }}</td>
                                    <td>
                                        @if (! $payment->wholesale_invoice_payment_id)
                                            <form action="{{ route('debts.payments.destroy', [$debt->id, $payment->id]) }}" method="POST" onsubmit="return confirm('حذف هذه الدفعة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @else
                                            <span class="text-muted small">من الفاتورة</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (! $debt->wholesaleInvoice && $debt->remaining_amount > 0)
                <form action="{{ route('debts.payments.store', $debt->id) }}" method="POST" class="row g-2 align-items-end bg-light rounded p-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">نقدي</label>
                        <input type="number" name="cash_amount" step="0.01" min="0" class="form-control" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">بنكي</label>
                        <input type="number" name="bank_amount" step="0.01" min="0" class="form-control" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">تاريخ الدفعة</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus"></i> تسجيل دفعة</button>
                    </div>
                    <div class="col-12">
                        <input type="text" name="notes" class="form-control" placeholder="ملاحظات (اختياري)">
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
