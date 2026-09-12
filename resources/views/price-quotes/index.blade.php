@extends('layout.app')

@section('title', 'عروض الأسعار')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title">💰 عروض الأسعار</h1>
        <p class="welcome-subtitle">Price Quotes — عروض أسعار بالعربي أو الإنجليزي</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
            style="border-radius: 15px; border: none; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="service-card card-primary" style="padding: 1.5rem;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1" style="color: var(--text-primary);">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                            قائمة عروض الأسعار
                        </h4>
                        <p class="mb-0 text-muted">إجمالي العروض: <strong>{{ $quotes->total() }}</strong></p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('invoices.index') }}" class="btn btn-lg"
                            style="background: #64748b; color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600;">
                            <i class="fas fa-file-invoice me-2"></i>
                            الفواتير
                        </a>
                        <a href="{{ route('price-quotes.create') }}" class="btn btn-lg"
                            style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(2, 132, 199, 0.3);">
                            <i class="fas fa-plus me-2"></i>
                            إضافة عرض سعر جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($quotes->count() > 0)
        <div class="row g-4">
            @foreach ($quotes as $quote)
                <div class="col-lg-6 col-xl-4">
                    <div class="service-card card-primary" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <span style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.9rem; font-weight: 600;">
                                {{ $quote->quote_number }}
                            </span>
                            <span style="background: #f1f5f9; color: #475569; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                                {{ $quote->language === 'ar' ? '🇵🇸 عربي' : '🇬🇧 English' }}
                            </span>
                        </div>

                        @if ($quote->valid_until)
                            <div style="margin-bottom: 1rem;">
                                @if ($quote->is_expired)
                                    <span style="background: #fee2e2; color: #b91c1c; padding: 0.35rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">
                                        <i class="fas fa-triangle-exclamation"></i> منتهي الصلاحية ({{ $quote->valid_until->format('Y-m-d') }})
                                    </span>
                                @else
                                    <span style="background: #d1fae5; color: #065f46; padding: 0.35rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">
                                        <i class="fas fa-circle-check"></i> صالح حتى {{ $quote->valid_until->format('Y-m-d') }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #0ea5e9; font-size: 1.2rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: #64748b;">العميل</p>
                                    <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1e293b;">{{ $quote->client_name }}</p>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 1.2rem;">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: #64748b;">التاريخ</p>
                                    <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #1e293b;">{{ $quote->quote_date->format('Y-m-d') }}</p>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: auto; padding-top: 1.5rem; border-top: 2px dashed #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 10px;">
                                <span style="color: #1e40af; font-weight: 600; font-size: 1rem;">
                                    <i class="fas fa-money-bill-wave" style="margin-left: 0.25rem;"></i>
                                    الإجمالي:
                                </span>
                                <span style="font-size: 1.6rem; font-weight: 700; color: #0ea5e9;">{{ number_format($quote->afterDiscount_amount, 2) }} {{ $quote->currency }}</span>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;">
                                <a href="{{ route('price-quotes.print', $quote->id) }}" class="btn btn-sm" target="_blank"
                                    style="background: #10b981; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('price-quotes.download-pdf', $quote->id) }}" class="btn btn-sm"
                                    style="background: #f59e0b; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="تحميل PDF">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('price-quotes.convert-to-invoice', $quote->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('تحويل هذا العرض لفاتورة عادية؟')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm w-100"
                                        style="background: #6d28d9; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="تحويل لفاتورة">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>
                                </form>
                                <form action="{{ route('price-quotes.destroy', $quote->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('هل أنت متأكد من حذف هذا العرض؟')">
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
            {{ $quotes->links() }}
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="service-card card-primary" style="text-align: center; padding: 4rem 2rem;">
                    <div style="margin-bottom: 2rem;">
                        <i class="fas fa-file-invoice-dollar" style="font-size: 5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="color: #64748b; margin-bottom: 1rem;">لا توجد عروض أسعار</h3>
                    <p style="color: #94a3b8; margin-bottom: 2rem;">ابدأ بإضافة عرض سعر جديد لعميلك</p>
                    <a href="{{ route('price-quotes.create') }}" class="btn btn-lg"
                        style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus"></i>
                        <span>إضافة عرض سعر جديد</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
