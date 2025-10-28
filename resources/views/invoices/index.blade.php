@extends('layout.app')

@section('title', 'إدارة الفواتير')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title">📋 إدارة الفواتير</h1>
        <p class="welcome-subtitle">عرض وإدارة جميع فواتير المعرض</p>
    </div>

    <!-- Success Message -->
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
                            <i class="fas fa-file-invoice text-primary me-2"></i>
                            قائمة الفواتير
                        </h4>
                        <p class="mb-0 text-muted">إجمالي الفواتير: <strong>{{ $invoices->total() }}</strong></p>
                    </div>
                    <a href="{{ route('invoices.create') }}" class="btn btn-lg"
                        style="background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(30, 64, 175, 0.3); transition: all 0.3s ease;">
                        <i class="fas fa-plus me-2"></i>
                        إضافة فاتورة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Grid -->
    @if ($invoices->count() > 0)
        <div class="row g-4">
            @foreach ($invoices as $invoice)
                <div class="col-lg-6 col-xl-4">
                    <div class="service-card card-primary" style="height: 100%; display: flex; flex-direction: column;">
                        <!-- Header -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                            <div>
                                <span
                                    style="background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); color: white; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                    {{ $invoice->invoice_number }}
                                </span>
                            </div>
                            <span
                                style="background: #ecfdf5; color: #065f46; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                                {{ $invoice->items->count() }} منتج
                            </span>
                        </div>

                        <!-- Customer Info -->
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                                <div
                                    style="width: 40px; height: 40px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1e40af; font-size: 1.2rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: #64748b;">اسم العميل</p>
                                    <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1e293b;">
                                        {{ $invoice->customer_name }}</p>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div
                                    style="width: 40px; height: 40px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 1.2rem;">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-size: 0.85rem; color: #64748b;">التاريخ</p>
                                    <p style="margin: 0; font-size: 1rem; font-weight: 600; color: #1e293b;">
                                        {{ $invoice->invoice_date->format('Y-m-d') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Amounts Section -->
                        <div style="margin-top: auto; padding-top: 1.5rem; border-top: 2px dashed #e2e8f0;">
                            <!-- الإجمالي قبل الخصم -->
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding: 0.5rem; background: #f8fafc; border-radius: 8px;">
                                <span style="color: #64748b; font-weight: 500; font-size: 0.9rem;">الإجمالي قبل
                                    الخصم:</span>
                                <span
                                    style="font-size: 1.1rem; font-weight: 600; color: #475569;">{{ number_format($invoice->total_amount, 2) }}
                                    شيكل</span>
                            </div>

                            <!-- الخصم -->
                            @if ($invoice->discount_amount > 0)
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding: 0.5rem; background: #fef3c7; border-radius: 8px;">
                                    <span style="color: #b45309; font-weight: 500; font-size: 0.9rem;">
                                        <i class="fas fa-tag" style="margin-left: 0.25rem;"></i>
                                        الخصم:
                                    </span>
                                    <span style="font-size: 1.1rem; font-weight: 600; color: #f59e0b;">-
                                        {{ number_format($invoice->discount_amount, 2) }} شيكل</span>
                                </div>
                            @endif

                            <!-- المبلغ النهائي -->
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 10px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);">
                                <span style="color: #065f46; font-weight: 600; font-size: 1rem;">
                                    <i class="fas fa-money-bill-wave" style="margin-left: 0.25rem;"></i>
                                    المبلغ النهائي:
                                </span>
                                <span
                                    style="font-size: 1.8rem; font-weight: 700; color: #10b981;">{{ number_format($invoice->afterDiscount_amount, 2) }}
                                    شيكل</span>
                            </div>

                            <!-- Actions -->
                            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem;">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm"
                                    style="background: #0ea5e9; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
                                    title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('invoices.print', $invoice->id) }}" class="btn btn-sm" target="_blank"
                                    style="background: #10b981; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
                                    title="طباعة الفاتورة">
                                    <i class="fas fa-print"></i>
                                </a>

                                <a href="{{ route('invoices.receipt', $invoice->id) }}" class="btn btn-sm" target="_blank"
                                    style="background: #8b5cf6; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
                                    title="سند قبض">
                                    <i class="fas fa-receipt"></i>
                                </a>

                                <a href="{{ route('invoices.download-pdf', $invoice->id) }}" class="btn btn-sm"
                                    style="background: #f59e0b; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
                                    title="تحميل PDF">
                                    <i class="fas fa-download"></i>
                                </a>

                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
                                    style="margin: 0;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm w-100"
                                        style="background: #ef4444; color: white; border: none; padding: 0.6rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; cursor: pointer;"
                                        title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $invoices->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="service-card card-primary" style="text-align: center; padding: 4rem 2rem;">
                    <div style="margin-bottom: 2rem;">
                        <i class="fas fa-inbox" style="font-size: 5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="color: #64748b; margin-bottom: 1rem;">لا توجد فواتير</h3>
                    <p style="color: #94a3b8; margin-bottom: 2rem;">ابدأ بإضافة فاتورة جديدة لمعرضك</p>
                    <a href="{{ route('invoices.create') }}" class="btn btn-lg"
                        style="background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(30, 64, 175, 0.3); display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus"></i>
                        <span>إضافة فاتورة جديدة</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    @push('styles')
        <style>
            .alert {
                animation: slideInDown 0.5s ease-out;
            }

            @keyframes slideInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .pagination .page-link {
                border-radius: 8px;
                margin: 0 0.25rem;
                border: none;
                color: #1e40af;
                font-weight: 600;
                padding: 0.5rem 1rem;
            }

            .pagination .page-item.active .page-link {
                background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
                color: white;
            }

            .pagination .page-link:hover {
                background: #f1f5f9;
                color: #1e40af;
            }
        </style>
    @endpush
@endsection
