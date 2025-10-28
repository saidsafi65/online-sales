@extends('layout.app')

@section('title', 'تعديل التسليم')

@push('styles')
<style>
    .edit-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .edit-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
        text-align: center;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: var(--shadow-lg);
        border: 2px solid var(--border-color);
    }

    .form-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-section-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .icon-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #0ea5e9;
    }

    .icon-money {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #10b981;
    }

    .icon-notes {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #f59e0b;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        color: var(--info-color);
    }

    .form-control {
        padding: 0.875rem 1rem;
        border-radius: 12px;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .form-control:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.15);
    }

    .input-group-text {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid var(--border-color);
        border-right: none;
        font-weight: 600;
        color: var(--text-primary);
    }

    .input-group .form-control {
        border-right: none;
        border-radius: 0 12px 12px 0;
    }

    .input-group-text:first-child {
        border-radius: 12px 0 0 12px;
        border-left: none;
    }

    .total-display {
        background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 2px solid #8b5cf6;
    }

    .total-label {
        color: #5b21b6;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .total-value {
        color: #6d28d9;
        font-weight: 900;
        font-size: 2rem;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .btn-submit {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        padding: 0.875rem 3rem;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-cancel {
        background: #64748b;
        color: white;
        padding: 0.875rem 3rem;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #ef4444;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }

        .button-group {
            flex-direction: column;
        }

        .btn-submit,
        .btn-cancel {
            width: 100%;
            justify-content: center;
        }

        .total-value {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="edit-container">
    <!-- Header -->
    <div class="edit-header">
        <h1 class="page-title">
            <i class="fas fa-edit ms-2"></i>
            تعديل التسليم
        </h1>
        <p class="page-subtitle">تحديث بيانات التسليم اليومي</p>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert-danger">
            <strong><i class="fas fa-exclamation-triangle ms-2"></i> يرجى تصحيح الأخطاء التالية:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form -->
    <form action="{{ route('daily-handovers.update', $dailyHandover) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="form-card mb-4">
            <!-- معلومات التاريخ والوقت -->
            <h3 class="form-section-title">
                <span class="form-section-icon icon-info">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                معلومات التاريخ والوقت
            </h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="handover_date" class="form-label">
                        <i class="fas fa-calendar"></i>
                        تاريخ التسليم
                    </label>
                    <input type="date" 
                           class="form-control @error('handover_date') is-invalid @enderror" 
                           id="handover_date" 
                           name="handover_date" 
                           value="{{ old('handover_date', $dailyHandover->handover_date->format('Y-m-d')) }}"
                           required>
                    @error('handover_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="handover_time" class="form-label">
                        <i class="fas fa-clock"></i>
                        وقت التسليم
                    </label>
                    <input type="time" 
                           class="form-control @error('handover_time') is-invalid @enderror" 
                           id="handover_time" 
                           name="handover_time" 
                           value="{{ old('handover_time', $dailyHandover->handover_time) }}"
                           required>
                    @error('handover_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-card mb-4">
            <!-- المبالغ المالية -->
            <h3 class="form-section-title">
                <span class="form-section-icon icon-money">
                    <i class="fas fa-money-bill-wave"></i>
                </span>
                المبالغ المالية
            </h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="cash" class="form-label">
                        <i class="fas fa-money-bill"></i>
                        مبلغ الكاش
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               class="form-control @error('cash') is-invalid @enderror" 
                               id="cash" 
                               name="cash" 
                               step="0.01" 
                               min="0"
                               value="{{ old('cash', $dailyHandover->cash) }}"
                               oninput="calculateTotal()"
                               required>
                        <span class="input-group-text">شيكل</span>
                        @error('cash')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="bank" class="form-label">
                        <i class="fas fa-university"></i>
                        مبلغ البنك
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               class="form-control @error('bank') is-invalid @enderror" 
                               id="bank" 
                               name="bank" 
                               step="0.01" 
                               min="0"
                               value="{{ old('bank', $dailyHandover->bank) }}"
                               oninput="calculateTotal()"
                               required>
                        <span class="input-group-text">شيكل</span>
                        @error('bank')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- إجمالي المبلغ -->
            <div class="total-display">
                <div class="total-label">
                    <i class="fas fa-calculator ms-1"></i>
                    إجمالي المبلغ المسلم
                </div>
                <div class="total-value" id="totalAmount">
                    {{ number_format($dailyHandover->cash + $dailyHandover->bank, 2) }} شيكل
                </div>
            </div>
        </div>

        <div class="form-card mb-4">
            <!-- تفاصيل إضافية -->
            <h3 class="form-section-title">
                <span class="form-section-icon icon-notes">
                    <i class="fas fa-sticky-note"></i>
                </span>
                تفاصيل إضافية
            </h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="reason" class="form-label">
                        <i class="fas fa-clipboard-list"></i>
                        سبب التسليم
                    </label>
                    <input type="text" 
                           class="form-control @error('reason') is-invalid @enderror" 
                           id="reason" 
                           name="reason" 
                           value="{{ old('reason', $dailyHandover->reason) }}"
                           placeholder="مثال: تسليم يومي"
                           required>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="received_by" class="form-label">
                        <i class="fas fa-user"></i>
                        استلمه
                    </label>
                    <input type="text" 
                           class="form-control @error('received_by') is-invalid @enderror" 
                           id="received_by" 
                           name="received_by" 
                           value="{{ old('received_by', $dailyHandover->received_by) }}"
                           placeholder="اسم المستلم">
                    @error('received_by')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">
                        <i class="fas fa-comment-alt"></i>
                        ملاحظات
                    </label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="4"
                              placeholder="أي ملاحظات إضافية...">{{ old('notes', $dailyHandover->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="button-group">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i>
                <span>حفظ التعديلات</span>
            </button>
            <a href="{{ route('daily-handovers.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i>
                <span>إلغاء</span>
            </a>
        </div>
    </form>
</div>
{{-- @endpush --}}

@push('scripts')
<script>
    function calculateTotal() {
        const cash = parseFloat(document.getElementById('cash').value) || 0;
        const bank = parseFloat(document.getElementById('bank').value) || 0;
        const total = cash + bank;
        
        document.getElementById('totalAmount').textContent = total.toFixed(2) + ' شيكل';
    }

    // حساب الإجمالي عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });
</script>
@endpush
@endsection