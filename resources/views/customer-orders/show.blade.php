@extends('layout.app')

@section('title', 'تفاصيل الطلب #' . $customerOrder->id)

@push('styles')
<style>
    .show-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        background: rgba(30, 64, 175, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 2rem;
    }

    .back-button:hover {
        background: rgba(30, 64, 175, 0.2);
        transform: translateX(5px);
        color: var(--primary-color);
    }

    .order-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .order-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .order-date {
        color: var(--text-secondary);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge-large {
        padding: 0.75rem 1.5rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge-large.pending {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }

    .status-badge-large.in-progress {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e3a8a;
    }

    .status-badge-large.completed {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-badge-large.cancelled {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--border-color);
    }

    .btn-edit-order {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
    }

    .btn-edit-order:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .btn-delete-order {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-delete-order:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .detail-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }

    .card-icon.customer {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: var(--primary-color);
    }

    .card-icon.device {
        background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
        color: #8b5cf6;
    }

    .card-icon.price {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #10b981;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .info-item {
        margin-bottom: 1rem;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: var(--text-primary);
        font-size: 1rem;
        font-weight: 500;
    }

    .phone-number {
        direction: ltr;
        text-align: right;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .phone-number i {
        color: var(--primary-color);
    }

    .specs-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        border: 2px solid var(--border-color);
        margin-bottom: 2rem;
    }

    .specs-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .specs-title i {
        color: var(--primary-color);
    }

    .specs-content {
        background: var(--light-bg);
        border-radius: 12px;
        padding: 1.5rem;
        line-height: 1.8;
        color: var(--text-primary);
        white-space: pre-wrap;
        border: 2px solid var(--border-color);
    }

    .notes-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        border: 2px solid #f59e0b;
    }

    .notes-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .notes-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 1rem;
    }

    .notes-content {
        color: #78350f;
        line-height: 1.7;
        font-weight: 500;
        white-space: pre-wrap;
    }

    .price-highlight {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        margin-top: 1rem;
    }

    .price-label {
        font-size: 0.875rem;
        color: #065f46;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .price-value {
        font-size: 2rem;
        font-weight: 900;
        color: #10b981;
    }

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

        .order-header {
            padding: 1.5rem;
        }

        .order-title {
            font-size: 1.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-edit-order,
        .btn-delete-order {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="show-container">
    <!-- Back Button -->
    <a href="{{ route('customer-orders.index') }}" class="back-button">
        <i class="fas fa-arrow-right"></i>
        <span>العودة للقائمة</span>
    </a>

    <!-- Order Header -->
    <div class="order-header">
        <div class="header-top">
            <div>
                <h1 class="order-title">
                    <i class="fas fa-file-invoice ms-2"></i>
                    طلب رقم #{{ $customerOrder->id }}
                </h1>
                <div class="order-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span>تاريخ الإضافة: {{ $customerOrder->created_at->format('Y/m/d - h:i A') }}</span>
                </div>
                @if($customerOrder->updated_at->ne($customerOrder->created_at))
                    <div class="order-date">
                        <i class="fas fa-sync-alt"></i>
                        <span>آخر تحديث: {{ $customerOrder->updated_at->format('Y/m/d - h:i A') }}</span>
                    </div>
                @endif
            </div>
            <div>
                <span class="status-badge-large {{ $customerOrder->status }}">
                    <i class="fas fa-circle"></i>
                    {{ $customerOrder->status_label }}
                </span>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('customer-orders.edit', $customerOrder) }}" class="btn-edit-order">
                <i class="fas fa-edit"></i>
                <span>تعديل الطلب</span>
            </a>
            <form action="{{ route('customer-orders.destroy', $customerOrder) }}" 
                  method="POST" 
                  style="display: inline;"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete-order">
                    <i class="fas fa-trash"></i>
                    <span>حذف الطلب</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="details-grid">
        <!-- Customer Info -->
        <div class="detail-card">
            <div class="card-icon customer">
                <i class="fas fa-user"></i>
            </div>
            <h3 class="card-title">معلومات الزبون</h3>
            <div class="info-item">
                <div class="info-label">الاسم</div>
                <div class="info-value">{{ $customerOrder->customer_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">رقم الجوال</div>
                <div class="info-value phone-number">
                    <i class="fas fa-phone"></i>
                    <span>{{ $customerOrder->phone_number }}</span>
                </div>
            </div>
        </div>

        <!-- Device Info -->
        <div class="detail-card">
            <div class="card-icon device">
                <i class="fas fa-laptop"></i>
            </div>
            <h3 class="card-title">معلومات الجهاز</h3>
            <div class="info-item">
                <div class="info-label">نوع الجهاز</div>
                <div class="info-value">{{ $customerOrder->device_type }}</div>
            </div>
        </div>

        <!-- Price Info -->
        <div class="detail-card">
            <div class="card-icon price">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h3 class="card-title">السعر التقريبي</h3>
            <div class="price-highlight">
                <div class="price-label">السعر المتوقع</div>
                <div class="price-value">{{ $customerOrder->approximate_price }}</div>
            </div>
        </div>
    </div>

    <!-- Specifications -->
    <div class="specs-card">
        <h3 class="specs-title">
            <i class="fas fa-list-ul"></i>
            المواصفات المطلوبة
        </h3>
        <div class="specs-content">{{ $customerOrder->specifications }}</div>
    </div>

    <!-- Notes -->
    @if($customerOrder->notes)
        <div class="notes-card">
            <div class="notes-icon">
                <i class="fas fa-sticky-note"></i>
            </div>
            <h3 class="notes-title">ملاحظات إضافية</h3>
            <div class="notes-content">{{ $customerOrder->notes }}</div>
        </div>
    @endif
</div>
@endsection