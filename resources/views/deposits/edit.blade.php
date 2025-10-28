@extends('layout.app')

@section('title', 'تعديل الأمانة')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">✏️ تعديل الأمانة</h1>
    <p class="welcome-subtitle">تحديث بيانات الأمانة</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <!-- عرض رسائل الخطأ -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 15px; border: none; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; margin-bottom: 2rem;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>يرجى تصحيح الأخطاء التالية:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- نموذج تعديل الأمانة -->
        <form action="{{ route('deposits.update', $deposit->id) }}" method="POST" id="depositForm">
            @csrf
            @method('PUT')

            <!-- معلومات الأمانة -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #1e40af;"></i>
                    معلومات الأمانة
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="piece" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-microchip" style="color: #10b981;"></i>
                            القطعة
                        </label>
                        <input type="text" 
                               class="form-control @error('piece') is-invalid @enderror" 
                               id="piece" 
                               name="piece" 
                               value="{{ old('piece', $deposit->piece) }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                               required>
                        @error('piece')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="type" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-tag" style="color: #1e40af;"></i>
                            النوع
                        </label>
                        <input type="text" 
                               class="form-control @error('type') is-invalid @enderror" 
                               id="type" 
                               name="type" 
                               value="{{ old('type', $deposit->type) }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                               required>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="reason" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clipboard-list" style="color: #f59e0b;"></i>
                            سبب الأخذ
                        </label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" 
                                  id="reason" 
                                  name="reason" 
                                  rows="3"
                                  style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem; resize: vertical;"
                                  required>{{ old('reason', $deposit->reason) }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- التواريخ -->
            <div class="service-card card-success mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calendar-alt" style="color: #10b981;"></i>
                    التواريخ والأوقات
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="taken_at" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clock" style="color: #f59e0b;"></i>
                            وقت الأخذ
                        </label>
                        <input type="datetime-local" 
                               class="form-control @error('taken_at') is-invalid @enderror" 
                               id="taken_at" 
                               name="taken_at" 
                               value="{{ old('taken_at', $deposit->taken_at ? \Carbon\Carbon::parse($deposit->taken_at)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem;"
                               required>
                        @error('taken_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="returned_at" class="form-label" style="font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i>
                            وقت الإرجاع
                        </label>
                        <input type="datetime-local" 
                               class="form-control @error('returned_at') is-invalid @enderror" 
                               id="returned_at" 
                               name="returned_at" 
                               value="{{ old('returned_at', $deposit->returned_at ? \Carbon\Carbon::parse($deposit->returned_at)->format('Y-m-d\TH:i') : '') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem;">
                        @error('returned_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" style="display: block; margin-top: 0.5rem;">
                            <i class="fas fa-info-circle"></i> اترك الحقل فارغاً إذا لم يتم الإرجاع بعد
                        </small>
                    </div>
                </div>
            </div>

            <!-- أزرار الحفظ -->
            <div class="text-center">
                <div class="d-inline-flex gap-3">
                    <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-save"></i>
                        <span>حفظ التعديلات</span>
                    </button>
                    <a href="{{ route('deposits.index') }}" class="btn btn-lg" style="background: #64748b; color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                        <i class="fas fa-times"></i>
                        <span>إلغاء</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .form-control:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.15) !important;
    }

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
</style>
@endpush
@endsection