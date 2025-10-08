@extends('layout.app')

@section('title', 'إضافة بضاعة مرجعة')

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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            color: #ef4444;
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
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
            padding: 0.875rem 2.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid var(--border-color);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-cancel:hover {
            background: rgba(100, 116, 139, 0.2);
            color: #64748b;
            border-color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title">إضافة بضاعة مرجعة</h1>
                <p class="form-subtitle">يرجى تعبئة البيانات المطلوبة لإضافة بضاعة مرجعة جديدة.</p>
            </div>

            <div class="form-card">
                <form action="{{ route('returned-goods.store') }}" method="POST">
                    @csrf
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-truck"></i>
                            معلومات المورد والمنتج
                        </div>

                        <div class="form-group">
                            <label for="supplier_name" class="form-label required">اسم المورد</label>
                            <input type="text" id="supplier_name" name="supplier_name"
                                class="form-control @error('supplier_name') is-invalid @enderror"
                                value="{{ old('supplier_name') }}" required>
                            @error('supplier_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="product_name" class="form-label required">اسم المنتج</label>
                            <select id="product_name" name="product_name"
                                class="form-control @error('product_name') is-invalid @enderror" required>
                                <option value="">اختر المنتج</option>
                                @foreach ($availableProducts as $product)
                                    <option value="{{ $product->product }}"
                                        {{ old('product_name') == $product->product ? 'selected' : '' }}>
                                        {{ $product->product }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-exclamation-circle"></i>
                            سبب الإرجاع
                        </div>

                        <div class="form-group">
                            <label for="reason" class="form-label required">سبب الإرجاع</label>
                            <textarea id="reason" name="reason" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-calendar"></i>
                            تاريخ الاكتشاف
                        </div>

                        <div class="form-group">
                            <label for="issue_discovered_date" class="form-label required">تاريخ الاكتشاف</label>
                            <input type="date" id="issue_discovered_date" name="issue_discovered_date"
                                class="form-control @error('issue_discovered_date') is-invalid @enderror"
                                value="{{ old('issue_discovered_date') }}" required>
                            @error('issue_discovered_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-check-circle"></i>
                            حالة الإرجاع
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label required">الحالة</label>
                            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror"
                                required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار
                                </option>
                                <option value="returned" {{ old('status') == 'returned' ? 'selected' : '' }}>تم الإرجاع
                                </option>
                                <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>تم الحل
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('returned-goods.index') }}" class="btn-cancel">إلغاء</a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
