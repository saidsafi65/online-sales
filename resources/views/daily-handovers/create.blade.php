@extends('layout.app')

@section('title', 'إضافة تسليم يومي')

@push('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .form-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: var(--shadow-lg);
        border: 2px solid var(--border-color);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }

    .form-label.required::after {
        content: ' *';
        color: #ef4444;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }

    .input-icon {
        position: relative;
    }

    .input-icon i {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }

    .input-icon .form-control {
        padding-right: 3rem;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 2rem;
        border-top: 2px solid var(--border-color);
        margin-top: 2rem;
    }

    .btn-submit {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        color: white;
        padding: 0.875rem 2.5rem;
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

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-cancel {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 0.875rem 2.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-submit,
        .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <!-- Header -->
    <div class="form-header">
        <h1 class="form-title">
            <i class="fas fa-plus-circle ms-2"></i>
            إضافة تسليم يومي جديد
        </h1>
        <p class="page-subtitle">قم بتعبئة بيانات التسليم المالي</p>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('daily-handovers.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">تاريخ التسليم</label>
                        <div class="input-icon">
                            <i class="fas fa-calendar"></i>
                            <input type="date" 
                                   name="handover_date" 
                                   class="form-control @error('handover_date') is-invalid @enderror"
                                   value="{{ old('handover_date', date('Y-m-d')) }}"
                                   required>
                        </div>
                        @error('handover_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label required">الساعة</label>
                        <div class="input-icon">
                            <i class="fas fa-clock"></i>
                            <input type="time" 
                                   name="handover_time" 
                                   class="form-control @error('handover_time') is-invalid @enderror"
                                   value="{{ old('handover_time', date('H:i')) }}"
                                   required>
                        </div>
                        @error('handover_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label required">الكاش (شيكل)</label>
                <div class="input-icon">
                    <i class="fas fa-shekel-sign"></i>
                    <input type="number" 
                           name="cash" 
                           step="0.01"
                           min="0"
                           class="form-control @error('cash') is-invalid @enderror"
                           value="{{ old('cash') }}"
                           placeholder="0.00"
                           required>
                </div>
                @error('cash')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label required">البنك (شيكل)</label>
                <div class="input-icon">
                    <i class="fas fa-shekel-sign"></i>
                    <input type="number" 
                           name="bank" 
                           step="0.01"
                           min="0"
                           class="form-control @error('bank') is-invalid @enderror"
                           value="{{ old('bank') }}"
                           placeholder="0.00"
                           required>
                </div>
                @error('bank')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label required">سبب التسليم</label>
                <div class="input-icon">
                    <i class="fas fa-comment"></i>
                    <input type="text" 
                           name="reason" 
                           class="form-control @error('reason') is-invalid @enderror"
                           value="{{ old('reason') }}"
                           placeholder="مثال: تسليم إيرادات اليوم، إيداع بنكي، ..."
                           required>
                </div>
                @error('reason')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">استلمه</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" 
                           name="received_by" 
                           class="form-control @error('received_by') is-invalid @enderror"
                           value="{{ old('received_by') }}"
                           placeholder="اسم الشخص المستلم (اختياري)">
                </div>
                @error('received_by')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">ملاحظات إضافية</label>
                <textarea name="notes" 
                          class="form-control @error('notes') is-invalid @enderror"
                          placeholder="أي ملاحظات أخرى...">{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('daily-handovers.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>إلغاء</span>
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    <span>حفظ التسليم</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
