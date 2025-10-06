@extends('layout.app')

@section('title', 'تفاصيل الجهاز - ' . $laptop->full_name)

@push('styles')
<style>
    .details-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
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
        margin-bottom: 1.5rem;
    }

    .back-button:hover {
        background: rgba(30, 64, 175, 0.2);
        transform: translateX(5px);
        color: var(--primary-color);
    }

    .laptop-info-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: var(--shadow-lg);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .laptop-image-container {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 3px solid var(--border-color);
    }

    .laptop-image {
        width: 100%;
        height: auto;
        display: block;
    }

    .laptop-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .laptop-description {
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .info-badge {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-right: 4px solid var(--primary-color);
        padding: 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-badge i {
        color: var(--primary-color);
        font-size: 1.5rem;
    }

    .info-badge-text {
        color: var(--text-primary);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .parts-section {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: var(--shadow-lg);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .section-title {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: var(--primary-color);
        font-size: 1.5rem;
    }

    .part-card {
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        animation: fadeIn 0.5s ease-out backwards;
    }

    .part-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-color);
    }

    .part-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
        padding: 1.75rem;
        border-bottom: 2px solid var(--border-color);
    }

    .part-title-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .part-type-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .part-number-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: var(--primary-color);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .part-price {
        text-align: left;
    }

    .price-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .price-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--success-color);
    }

    .specifications-box {
        margin-top: 1.25rem;
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid var(--border-color);
    }

    .spec-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .spec-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .spec-item {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .spec-key {
        font-weight: 600;
        color: var(--text-primary);
    }

    .part-body {
        padding: 2rem;
    }

    .compatible-section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .compatible-section-title i {
        color: var(--success-color);
    }

    .compatible-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .compatible-card {
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        background: white;
    }

    .compatible-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-color);
    }

    .compatible-card.current {
        background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
        border-color: var(--primary-color);
    }

    .compatible-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .compatible-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid var(--border-color);
    }

    .compatible-placeholder {
        width: 70px;
        height: 70px;
        background: var(--light-bg);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--border-color);
    }

    .compatible-placeholder i {
        font-size: 2rem;
        color: var(--text-secondary);
    }

    .compatible-info {
        flex: 1;
    }

    .compatible-brand {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .compatible-model {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .current-badge {
        display: inline-block;
        background: var(--primary-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .details-link {
        display: block;
        text-align: center;
        margin-top: 1rem;
        padding: 0.625rem;
        color: var(--primary-color);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        border-radius: 8px;
        background: rgba(30, 64, 175, 0.1);
        transition: all 0.3s ease;
    }

    .details-link:hover {
        background: rgba(30, 64, 175, 0.2);
        transform: translateX(-3px);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .tip-box {
        margin-top: 2rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-right: 4px solid var(--warning-color);
        padding: 1.5rem;
        border-radius: 16px;
        display: flex;
        gap: 1rem;
    }

    .tip-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--warning-color);
        font-size: 1.25rem;
    }

    .tip-content {
        flex: 1;
    }

    .tip-title {
        font-size: 1rem;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 0.5rem;
    }

    .tip-text {
        font-size: 0.9rem;
        color: #78350f;
        line-height: 1.6;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .part-card:nth-child(1) { animation-delay: 0.1s; }
    .part-card:nth-child(2) { animation-delay: 0.2s; }
    .part-card:nth-child(3) { animation-delay: 0.3s; }
    .part-card:nth-child(4) { animation-delay: 0.4s; }
    .part-card:nth-child(5) { animation-delay: 0.5s; }

    @media (max-width: 768px) {
        .laptop-info-card {
            padding: 1.5rem;
        }

        .laptop-title {
            font-size: 1.5rem;
        }

        .parts-section {
            padding: 1.5rem;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .compatible-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <a href="{{ route('compatibility.index') }}" class="back-button">
        <i class="fas fa-arrow-right"></i>
        <span>العودة للقائمة</span>
    </a>

    <!-- معلومات الجهاز -->
    <div class="laptop-info-card">
        <div class="row align-items-center">
            @if($laptop->image)
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="laptop-image-container">
                    <img src="{{ asset('storage/' . $laptop->image) }}" 
                         alt="{{ $laptop->full_name }}" 
                         class="laptop-image">
                </div>
            </div>
            @endif
            <div class="col-md-8">
                <h1 class="laptop-title">
                    <i class="fas fa-laptop ms-2"></i>
                    {{ $laptop->brand }} {{ $laptop->model }}
                </h1>
                @if($laptop->description)
                    <p class="laptop-description">{{ $laptop->description }}</p>
                @endif
                <div class="info-badge">
                    <i class="fas fa-info-circle"></i>
                    <span class="info-badge-text">
                        يحتوي هذا الجهاز على <strong>{{ $laptop->parts->count() }}</strong> قطعة مسجلة
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول القطع والتوافقات -->
    <div class="parts-section">
        <h2 class="section-title">
            <i class="fas fa-tools"></i>
            <span>قطع الجهاز والتوافقات</span>
        </h2>

        @if($laptop->parts->count() > 0)
            <div class="parts-list">
                @foreach($partTypes as $partType)
                    @php
                        $compatData = $compatibilityData[$partType->id] ?? null;
                    @endphp

                    @if($compatData)
                        <div class="part-card">
                            <!-- رأس القطعة -->
                            <div class="part-header">
                                <div class="part-title-row">
                                    <div>
                                        <h3 class="part-type-name">{{ $partType->name }}</h3>
                                        <span class="part-number-badge">
                                            <i class="fas fa-hashtag"></i>
                                            {{ $compatData['part']->part_number }}
                                        </span>
                                    </div>
                                    @if($compatData['part']->price)
                                        <div class="part-price">
                                            <div class="price-label">السعر</div>
                                            <div class="price-value">
                                                {{ number_format($compatData['part']->price, 2) }} شيكل
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($compatData['part']->specifications)
                                    <div class="specifications-box">
                                        <div class="spec-title">
                                            <i class="fas fa-list-ul ms-2"></i>
                                            المواصفات
                                        </div>
                                        <div class="spec-grid">
                                            @foreach(json_decode($compatData['part']->specifications, true) as $key => $value)
                                                <div class="spec-item">
                                                    <span class="spec-key">{{ ucfirst($key) }}:</span>
                                                    {{ $value }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- الأجهزة المتوافقة -->
                            <div class="part-body">
                                <h4 class="compatible-section-title">
                                    <i class="fas fa-laptop"></i>
                                    <span>الأجهزة المتوافقة ({{ $compatData['compatible_laptops']->count() }})</span>
                                </h4>

                                @if($compatData['compatible_laptops']->count() > 0)
                                    <div class="compatible-grid">
                                        @foreach($compatData['compatible_laptops'] as $compatLaptop)
                                            <div class="compatible-card {{ $compatLaptop->id == $laptop->id ? 'current' : '' }}">
                                                <div class="compatible-content">
                                                    @if($compatLaptop->image)
                                                        <img src="{{ asset('storage/' . $compatLaptop->image) }}" 
                                                             alt="{{ $compatLaptop->full_name }}"
                                                             class="compatible-image">
                                                    @else
                                                        <div class="compatible-placeholder">
                                                            <i class="fas fa-laptop"></i>
                                                        </div>
                                                    @endif
                                                    <div class="compatible-info">
                                                        <div class="compatible-brand">{{ $compatLaptop->brand }}</div>
                                                        <div class="compatible-model">{{ $compatLaptop->model }}</div>
                                                        @if($compatLaptop->id == $laptop->id)
                                                            <span class="current-badge">
                                                                <i class="fas fa-check ms-1"></i>
                                                                الجهاز الحالي
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($compatLaptop->id != $laptop->id)
                                                    <a href="{{ route('compatibility.show', $compatLaptop->id) }}" 
                                                       class="details-link">
                                                        عرض التفاصيل
                                                        <i class="fas fa-arrow-left me-1"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p class="empty-state-text">لا توجد أجهزة متوافقة أخرى حالياً</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-tools"></i>
                <p class="empty-state-text">لا توجد قطع مسجلة لهذا الجهاز</p>
            </div>
        @endif
    </div>

    <!-- نصائح -->
    <div class="tip-box">
        <div class="tip-icon">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="tip-content">
            <h3 class="tip-title">
                <i class="fas fa-exclamation-circle ms-2"></i>
                نصيحة مهمة
            </h3>
            <p class="tip-text">
                تأكد دائماً من التوافق الفعلي للقطع قبل الشراء. الأرقام المذكورة هي إرشادية ويُنصح بالتحقق من الموصلات والأبعاد الفعلية للقطع قبل اتخاذ قرار الشراء.
            </p>
        </div>
    </div>
</div>
@endsection