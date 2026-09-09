@extends('layout.app')

@section('title', 'فاتورة جملة - '.$invoice->invoice_number)

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">📦 فاتورة جملة {{ $invoice->invoice_number }}</h1>
    <p class="welcome-subtitle">تفاصيل فاتورة البيع بالجملة الكاملة</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- أزرار الإجراءات -->
        <div class="service-card card-primary mb-4" style="padding: 1.25rem;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('wholesale-invoices.index') }}" class="btn" style="background: #64748b; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600;">
                    <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                </a>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('wholesale-invoices.print', $invoice->id) }}" target="_blank" class="btn" style="background: #10b981; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-print me-1"></i> طباعة
                    </a>
                    <a href="{{ route('wholesale-invoices.download-pdf', $invoice->id) }}" class="btn" style="background: #f59e0b; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600;">
                        <i class="fas fa-download me-1"></i> تحميل PDF
                    </a>
                    <form action="{{ route('wholesale-invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn" style="background: #ef4444; color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600; border: none;">
                            <i class="fas fa-trash me-1"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- بيانات المحل المشتري -->
        <div class="service-card card-primary mb-4">
            <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-store" style="color: #b91c1c;"></i>
                بيانات المحل المشتري
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-store me-1"></i> اسم المحل / المعرض</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->buyer_store_name }}</div>
                    </div>
                </div>
                @if ($invoice->buyer_tax_number)
                    <div class="col-md-6">
                        <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                            <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-id-card me-1"></i> الرقم الضريبي</div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->buyer_tax_number }}</div>
                        </div>
                    </div>
                @endif
                @if ($invoice->buyer_phone)
                    <div class="col-md-6">
                        <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                            <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-phone me-1"></i> الهاتف</div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->buyer_phone }}</div>
                        </div>
                    </div>
                @endif
                @if ($invoice->buyer_address)
                    <div class="col-md-6">
                        <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                            <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-map-marker-alt me-1"></i> العنوان</div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->buyer_address }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- معلومات الفاتورة -->
        <div class="service-card card-primary mb-4">
            <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-info-circle" style="color: #b91c1c;"></i>
                معلومات الفاتورة
            </h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-calendar me-1"></i> التاريخ</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->invoice_date->format('Y-m-d') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-hashtag me-1"></i> رقم الفاتورة</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ $invoice->invoice_number }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b;"><i class="fas fa-hand-holding-usd me-1"></i> شروط الدفع</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">{{ \App\Models\WholesaleInvoice::PAYMENT_TERMS_LABELS[$invoice->payment_terms] }}</div>
                    </div>
                </div>
                @if ($invoice->payment_terms === 'credit' && $invoice->due_date)
                    <div class="col-md-3">
                        <div style="background: #fef3c7; border-radius: 10px; padding: 1rem;">
                            <div style="font-size: 0.85rem; color: #b45309;"><i class="fas fa-calendar-check me-1"></i> تاريخ الاستحقاق</div>
                            <div style="font-size: 1.1rem; font-weight: 700; color: #b45309;">{{ $invoice->due_date->format('Y-m-d') }}</div>
                        </div>
                    </div>
                @endif
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

        <!-- الدفعات -->
        <div class="service-card card-primary mb-4">
            @php
                $statusColors = [
                    'open' => ['bg' => '#fee2e2', 'text' => '#b91c1c'],
                    'partial' => ['bg' => '#fef3c7', 'text' => '#b45309'],
                    'paid' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                ];
                $sc = $statusColors[$invoice->payment_status];
            @endphp
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 style="color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-hand-holding-dollar" style="color: #b91c1c;"></i>
                    الدفعات
                </h5>
                <span class="badge" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; font-weight: 700; padding: 0.5rem 1rem; font-size: 0.9rem;">
                    {{ \App\Models\WholesaleInvoice::PAYMENT_STATUS_LABELS[$invoice->payment_status] }}
                </span>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 1rem; text-align: center;">
                        <div style="font-size: 0.8rem; color: #64748b;">المطلوب</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: #1e293b;">{{ number_format($invoice->afterDiscount_amount, 2) }} شيكل</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #f0fdf4; border-radius: 10px; padding: 1rem; text-align: center;">
                        <div style="font-size: 0.8rem; color: #059669;">المدفوع</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: #059669;">{{ number_format($invoice->paid_amount, 2) }} شيكل</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #fef2f2; border-radius: 10px; padding: 1rem; text-align: center;">
                        <div style="font-size: 0.8rem; color: #b91c1c;">المتبقي</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: #b91c1c;">{{ number_format($invoice->remaining_amount, 2) }} شيكل</div>
                    </div>
                </div>
            </div>

            @if ($invoice->payments->isNotEmpty())
                <div style="overflow-x: auto; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
                    <table class="table mb-0" style="font-size: 0.9rem;">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th style="padding: 0.6rem 0.9rem;">التاريخ</th>
                                <th>نقدي</th>
                                <th>بنكي</th>
                                <th>المبلغ</th>
                                <th>استلمها</th>
                                <th>ملاحظات</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->payments->sortByDesc('payment_date') as $payment)
                                <tr>
                                    <td style="padding: 0.6rem 0.9rem;">{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td>{{ number_format($payment->cash_amount, 2) }}</td>
                                    <td>{{ number_format($payment->bank_amount, 2) }}</td>
                                    <td style="font-weight: 700;">{{ number_format($payment->total_amount, 2) }}</td>
                                    <td>{{ $payment->received_by }}</td>
                                    <td>{{ $payment->notes }}</td>
                                    <td>
                                        <form action="{{ route('wholesale-invoices.payments.destroy', [$invoice->id, $payment->id]) }}" method="POST" onsubmit="return confirm('حذف هذه الدفعة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #b91c1c; border: none; padding: 0.3rem 0.6rem; border-radius: 6px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($invoice->remaining_amount > 0)
                <form action="{{ route('wholesale-invoices.payments.store', $invoice->id) }}" method="POST" class="row g-2 align-items-end" style="background: #f8fafc; border-radius: 10px; padding: 1rem;">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">نقدي</label>
                        <input type="number" name="cash_amount" step="0.01" min="0" class="form-control" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">بنكي</label>
                        <input type="number" name="bank_amount" step="0.01" min="0" class="form-control" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">تاريخ الدفعة</label>
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
