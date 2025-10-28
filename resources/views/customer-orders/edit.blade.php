@extends('layout.app')

@section('title', 'تعديل الطلب')

@push('styles')
    <style>
        .form-container {
            max-width: 900px;
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-subtitle {
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

        .form-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title i {
            color: var(--primary-color);
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
            color: var(--text-primary);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: block;
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            font-size: 1rem;
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
            border: 2px solid transparent;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
        }

        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #ef4444;
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
                إضافة طلب جديد
            </h1>
            <p class="form-subtitle">قم بتعبئة بيانات طلب الزبون</p>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form action="{{ route('customer-orders.update', $customerOrder) }}" method="POST" class="needs-validation"
                novalidate>
                @csrf
                @method('PUT')
                <!-- معلومات الزبون -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        معلومات الزبون
                    </h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">اسم الزبون</label>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="customer_name"
                                        class="form-control @error('customer_name') is-invalid @enderror"
                                        value="{{ old('customer_name', $customerOrder->customer_name) }}"
                                        placeholder="أدخل اسم الزبون" required>
                                </div>
                                @error('customer_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">رقم الجوال</label>
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="text" name="phone_number"
                                        class="form-control @error('phone_number') is-invalid @enderror"
                                        value="{{ old('phone_number', $customerOrder->phone_number) }}"
                                        placeholder="05xxxxxxxx" required>
                                </div>
                                @error('phone_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات الجهاز -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-laptop"></i>
                        معلومات الجهاز
                    </h3>

                    <div class="form-group">
                        <label class="form-label required">نوع الجهاز</label>
                        <div class="input-icon">
                            <i class="fas fa-laptop"></i>
                            <input type="text" name="device_type"
                                class="form-control @error('device_type') is-invalid @enderror"
                                value="{{ old('device_type', $customerOrder->device_type) }}"
                                placeholder="مثال: Laptop HP 15s, MacBook Pro 2020" required>
                        </div>
                        @error('device_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">المواصفات</label>
                        <textarea name="specifications" class="form-control @error('specifications') is-invalid @enderror"
                            placeholder="أدخل مواصفات الجهاز المطلوب: المعالج، الرام، التخزين، كرت الشاشة، إلخ..." required>{{ old('specifications', $customerOrder->specifications) }}</textarea>
                        @error('specifications')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- معلومات الطلب -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-dollar-sign"></i>
                        معلومات السعر
                    </h3>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">السعر التقريبي</label>
                                <div class="input-icon">
                                    <i class="fas fa-shekel-sign"></i>
                                    <input type="text" name="approximate_price"
                                        class="form-control @error('approximate_price') is-invalid @enderror"
                                        value="{{ old('approximate_price', $customerOrder->approximate_price) }}"
                                        placeholder="مثال: 2000-2500 شيكل" required>
                                </div>
                                @error('approximate_price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">حالة الطلب</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="pending"
                                        {{ old('status', $customerOrder->status) == 'pending' ? 'selected' : '' }}>قيد
                                        الانتظار
                                    </option>
                                    <option value="in_progress"
                                        {{ old('status', $customerOrder->status) == 'in_progress' ? 'selected' : '' }}>قيد
                                        التنفيذ</option>
                                    <option value="completed"
                                        {{ old('status', $customerOrder->status) == 'completed' ? 'selected' : '' }}>مكتمل
                                    </option>
                                    <option value="cancelled"
                                        {{ old('status', $customerOrder->status) == 'cancelled' ? 'selected' : '' }}>ملغي
                                    </option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ملاحظات إضافية</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                            placeholder="أي ملاحظات أو تفاصيل إضافية...">{{ old('notes', $customerOrder->notes) }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ route('customer-orders.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        <span>إلغاء</span>
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        <span>حفظ الطلب</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
