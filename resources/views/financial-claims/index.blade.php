@extends('layout.app')

@section('title', 'المطالبات المالية')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title">📄 المطالبات المالية</h1>
        <p class="welcome-subtitle">إدارة مطالبات الدفع الموجّهة للجهات الخارجية</p>
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
                            قائمة المطالبات
                        </h4>
                        <p class="mb-0 text-muted">الإجمالي: <strong>{{ $claims->total() }}</strong></p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('invoices.create') }}" class="btn"
                            style="background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%); color: white; padding: 12px 24px; border-radius: 50px; border: none; font-weight: 600;">
                            <i class="fas fa-plus me-2"></i> إضافة فاتورة جديدة
                        </a>
                        <a href="{{ route('financial-claims.create') }}" class="btn"
                            style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 12px 24px; border-radius: 50px; border: none; font-weight: 600;">
                            <i class="fas fa-file-invoice-dollar me-2"></i> مطالبة مالية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($claims->count() > 0)
        <div class="row g-4">
            @foreach ($claims as $claim)
                <div class="col-lg-6 col-xl-4">
                    <div class="service-card card-primary" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                            <span style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600;">
                                {{ $claim->claim_reference }}
                            </span>
                            <span style="background: {{ $claim->language === 'ar' ? '#ecfdf5' : '#eff6ff' }}; color: {{ $claim->language === 'ar' ? '#065f46' : '#1d4ed8' }}; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                                {{ $claim->language === 'ar' ? 'عربي' : 'English' }}
                            </span>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <p style="margin: 0; font-size: 0.85rem; color: #64748b;">الجهة</p>
                            <p style="margin: 0 0 0.5rem; font-size: 1.05rem; font-weight: 600; color: #1e293b;">{{ $claim->organization_name }}</p>
                            <p style="margin: 0; font-size: 0.85rem; color: #64748b;">التاريخ</p>
                            <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #1e293b;">{{ $claim->claim_date->format('Y-m-d') }}</p>
                        </div>

                        <div style="margin-top: auto; padding-top: 1rem; border-top: 2px dashed #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 10px;">
                                <span style="color: #1e40af; font-weight: 600; font-size: 0.95rem;">
                                    <i class="fas fa-money-bill-wave me-1"></i> الإجمالي
                                </span>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">{{ number_format($claim->total_amount, 2) }} {{ $claim->currency }}</span>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                                <a href="{{ route('financial-claims.print', $claim->id) }}" target="_blank" class="btn btn-sm"
                                    style="background: #10b981; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="عرض/طباعة">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('financial-claims.download-pdf', $claim->id) }}" class="btn btn-sm"
                                    style="background: #f59e0b; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="تحميل PDF">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('financial-claims.destroy', $claim->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المطالبة؟')">
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
            {{ $claims->links() }}
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="service-card card-primary" style="text-align: center; padding: 4rem 2rem;">
                    <div style="margin-bottom: 2rem;">
                        <i class="fas fa-file-invoice-dollar" style="font-size: 5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="color: #64748b; margin-bottom: 1rem;">لا توجد مطالبات مالية</h3>
                    <p style="color: #94a3b8; margin-bottom: 2rem;">ابدأ بإضافة مطالبة مالية جديدة</p>
                    <a href="{{ route('financial-claims.create') }}" class="btn btn-lg"
                        style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus"></i>
                        <span>إضافة مطالبة مالية</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
