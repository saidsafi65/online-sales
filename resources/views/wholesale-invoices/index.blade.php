@extends('layout.app')

@section('title', 'فواتير البيع بالجملة')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title">📦 فواتير البيع بالجملة</h1>
        <p class="welcome-subtitle">فواتير البيع بين معرضك ومحلات/معارض تانية (B2B)</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
            style="border-radius: 15px; border: none; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Actions Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="service-card card-primary" style="padding: 1.5rem;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1" style="color: var(--text-primary);">
                            <i class="fas fa-boxes-stacked text-primary me-2"></i>
                            قائمة فواتير الجملة
                        </h4>
                        <p class="mb-0 text-muted">إجمالي الفواتير: <strong>{{ $invoices->total() }}</strong></p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('invoices.index') }}" class="btn btn-lg"
                            style="background: #64748b; color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600;">
                            <i class="fas fa-file-invoice me-2"></i>
                            فواتير العملاء العادية
                        </a>
                        <a href="{{ route('wholesale-invoices.create') }}" class="btn btn-lg"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); transition: all 0.3s ease;">
                            <i class="fas fa-plus me-2"></i>
                            إضافة فاتورة جملة جديدة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($invoices->count() > 0)
        <div class="row g-4">
            @foreach ($invoices as $invoice)
                <div class="col-lg-6 col-xl-4">
                    <div class="service-card card-primary" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                {{ $invoice->invoice_number }}
                            </span>
                            <span style="background: {{ $invoice->payment_terms === 'credit' ? '#fef3c7' : '#ecfdf5' }}; color: {{ $invoice->payment_terms === 'credit' ? '#b45309' : '#065f46' }}; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                                {{ \App\Models\WholesaleInvoice::PAYMENT_TERMS_LABELS[$invoice->payment_terms] }}
                            </span>
                        </div>

                        @php
                            $paymentStatusColors = [
                                'open' => ['bg' => '#fee2e2', 'text' => '#b91c1c'],
                                'partial' => ['bg' => '#fef3c7', 'text' => '#b45309'],
                                'paid' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                            ];
                            $psc = $paymentStatusColors[$invoice->payment_status];
                        @endphp
                        <div style="margin-bottom: 1rem;">
                            <span style="background: {{ $psc['bg'] }}; color: {{ $psc['text'] }}; padding: 0.35rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                {{ \App\Models\WholesaleInvoice::PAYMENT_STATUS_LABELS[$invoice->payment_status] }}
                            </span>
                            @if ($invoice->payment_terms === 'credit' && $invoice->due_date)
                                <span class="text-muted small ms-1">
                                    <i class="fas fa-calendar-check"></i> يستحق: {{ $invoice->due_date->format('Y-m-d') }}
                                </span>
                            @endif
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 1.2rem;">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: #64748b;">المحل المشتري</p>
                                    <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1e293b;">{{ $invoice->buyer_store_name }}</p>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 1.2rem;">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: #64748b;">التاريخ</p>
                                    <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #1e293b;">{{ $invoice->invoice_date->format('Y-m-d') }}</p>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: auto; padding-top: 1.5rem; border-top: 2px dashed #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 10px;">
                                <span style="color: #065f46; font-weight: 600; font-size: 1rem;">
                                    <i class="fas fa-money-bill-wave" style="margin-left: 0.25rem;"></i>
                                    المبلغ النهائي:
                                </span>
                                <span style="font-size: 1.6rem; font-weight: 700; color: #10b981;">{{ number_format($invoice->afterDiscount_amount, 2) }} شيكل</span>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;">
                                <a href="{{ route('wholesale-invoices.show', $invoice->id) }}" class="btn btn-sm"
                                    style="background: #0ea5e9; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('wholesale-invoices.print', $invoice->id) }}" class="btn btn-sm" target="_blank"
                                    style="background: #10b981; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('wholesale-invoices.download-pdf', $invoice->id) }}" class="btn btn-sm"
                                    style="background: #f59e0b; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="تحميل PDF">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('wholesale-invoices.destroy', $invoice->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm w-100"
                                        style="background: #ef4444; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $invoices->links() }}
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="service-card card-primary" style="text-align: center; padding: 4rem 2rem;">
                    <div style="margin-bottom: 2rem;">
                        <i class="fas fa-boxes-stacked" style="font-size: 5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="color: #64748b; margin-bottom: 1rem;">لا توجد فواتير جملة</h3>
                    <p style="color: #94a3b8; margin-bottom: 2rem;">ابدأ بإضافة فاتورة بيع بالجملة لمحل أو معرض تاني</p>
                    <a href="{{ route('wholesale-invoices.create') }}" class="btn btn-lg"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus"></i>
                        <span>إضافة فاتورة جملة جديدة</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
