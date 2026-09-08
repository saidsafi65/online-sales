@extends('layout.app')

@section('title', 'فاتورة - '.$invoice->invoice_number)

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">🧾 فاتورة {{ $invoice->invoice_number }}</h1>
    <p class="welcome-subtitle">تفاصيل الفاتورة الكاملة</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- أزرار الإجراءات -->
        <div class="service-card card-primary mb-4" style="padding: 1.25rem;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('invoices.index') }}" class="btn" style="background: #64748b; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600;">
                    <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                </a>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="btn" style="background: #10b981; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-print me-1"></i> طباعة
                    </a>
                    <a href="{{ route('invoices.receipt', $invoice->id) }}" target="_blank" class="btn" style="background: #8b5cf6; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-receipt me-1"></i> سند قبض
                    </a>
                    <a href="{{ route('invoices.download-pdf', $invoice->id) }}" class="btn" style="background: #f59e0b; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-download me-1"></i> تحميل PDF
                    </a>
                    <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn" style="background: #ef4444; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600; border: none;">
                            <i class="fas fa-trash me-1"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- معلومات الفاتورة -->
        <div class="service-card card-primary mb-4">
            <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-info-circle" style="color: #b91c1c;"></i>
                معلومات الفاتورة
            </h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-user me-1"></i> اسم العميل</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->customer_name }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-calendar me-1"></i> التاريخ</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-hashtag me-1"></i> رقم الفاتورة</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->invoice_number }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المنتجات -->
        <div class="service-card card-success mb-4">
            <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-box" style="color: #10b981;"></i>
                المنتجات
            </h5>
            <div style="overflow-x: auto; border-radius: 12px; border: 2px solid #e2e8f0;">
                <table class="table mb-0">
                    <thead style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white;">
                        <tr>
                            <th style="padding: 1rem; text-align: center;">رقم</th>
                            <th style="padding: 1rem;">الوصف</th>
                            <th style="padding: 1rem; text-align: center;">الكمية</th>
                            <th style="padding: 1rem; text-align: center;">السعر</th>
                            <th style="padding: 1rem; text-align: center;">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td style="padding: 0.85rem; text-align: center;">{{ $item->item_number }}</td>
                                <td style="padding: 0.85rem;">{{ $item->description }}</td>
                                <td style="padding: 0.85rem; text-align: center;">{{ $item->quantity }}</td>
                                <td style="padding: 0.85rem; text-align: center;">{{ number_format($item->unit_price, 2) }}</td>
                                <td style="padding: 0.85rem; text-align: center; font-weight: 700;">{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc;">
                            <td colspan="4" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 1.05rem;">الإجمالي قبل الخصم</td>
                            <td style="padding: 1rem; text-align: center; font-weight: 700; font-size: 1.1rem; color: #475569;">{{ number_format($invoice->total_amount, 2) }} شيكل</td>
                        </tr>
                        @if ($invoice->discount_amount != 0)
                            <tr style="background: #fef3c7;">
                                <td colspan="4" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 1.05rem; color: #b45309;"><i class="fas fa-tag me-1"></i> الخصم</td>
                                <td style="padding: 1rem; text-align: center; font-weight: 700; font-size: 1.1rem; color: #f59e0b;">{{ number_format($invoice->discount_amount, 2) }} شيكل</td>
                            </tr>
                        @endif
                        <tr style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                            <td colspan="4" style="padding: 1.1rem; text-align: left; font-weight: 700; font-size: 1.15rem; color: #065f46;"><i class="fas fa-money-bill-wave me-1"></i> المبلغ النهائي</td>
                            <td style="padding: 1.1rem; text-align: center; font-weight: 700; font-size: 1.3rem; color: #10b981;">{{ number_format($invoice->afterDiscount_amount, 2) }} شيكل</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="service-card card-warning mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-sticky-note" style="color: #f59e0b;"></i>
                    ملاحظات
                </h5>
                <p style="margin: 0; color: #475569; line-height: 1.7; white-space: pre-wrap;">{{ $invoice->notes }}</p>
            </div>
        @endif

    </div>
</div>
@endsection
