@extends('layout.app')

@section('title', 'إضافة مطالبة مالية')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">📄 إضافة مطالبة مالية</h1>
    <p class="welcome-subtitle">Financial Claim — طلب دفع موجّه لجهة خارجية</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-11 col-xl-10">
        <form method="POST" action="{{ route('financial-claims.store') }}" id="claimForm">
            @csrf

            <!-- اللغة -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-language" style="color: #0ea5e9;"></i>
                    لغة المستند
                </h5>
                <div class="d-flex gap-3">
                    <label style="flex:1; cursor:pointer; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem; display:flex; align-items:center; gap:0.6rem;" class="lang-option">
                        <input type="radio" name="language" value="ar" checked style="width:1.2rem;height:1.2rem;">
                        <span style="font-weight:600;">🇵🇸 عربي</span>
                    </label>
                    <label style="flex:1; cursor:pointer; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem; display:flex; align-items:center; gap:0.6rem;" class="lang-option">
                        <input type="radio" name="language" value="en" style="width:1.2rem;height:1.2rem;">
                        <span style="font-weight:600;">🇬🇧 English</span>
                    </label>
                </div>
            </div>

            <!-- معلومات المطالبة -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #0ea5e9;"></i>
                    معلومات المطالبة
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-weight: 600;">رقم المطالبة</label>
                        <input type="text" class="form-control" value="{{ $claimReference }}" disabled
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; background:#f8fafc;">
                        <div class="form-text">بيتحدد تلقائياً حسب تاريخ المطالبة عند الحفظ</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-weight: 600;">التاريخ</label>
                        <input type="date" name="claim_date" class="form-control @error('claim_date') is-invalid @enderror"
                               value="{{ old('claim_date', date('Y-m-d')) }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                        @error('claim_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-weight: 600;">رقم ToR</label>
                        <input type="text" name="tor_number" class="form-control" value="{{ old('tor_number') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-weight: 600;">العملة</label>
                        <input type="text" name="currency" class="form-control" value="{{ old('currency', 'ILS') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                    </div>
                </div>
            </div>

            <!-- بيانات المستلم -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-building" style="color: #0ea5e9;"></i>
                    بيانات المستلم (الجهة الموجّهة لها المطالبة)
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600;">المؤسسة / الجهة</label>
                        <input type="text" name="organization_name" class="form-control @error('organization_name') is-invalid @enderror"
                               value="{{ old('organization_name') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                        @error('organization_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">للعناية (اسم الشخص)</label>
                        <input type="text" name="attention_name" class="form-control" value="{{ old('attention_name') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">المنصب</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                </div>
            </div>

            <!-- بنود المطالبة -->
            <div class="service-card card-success mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-list" style="color: #10b981;"></i>
                        بنود المطالبة
                    </h5>
                    <button type="button" class="btn" onclick="addItem()" style="background: #10b981; color: white; padding: 0.6rem 1.5rem; border-radius: 10px; border: none; font-weight: 600;">
                        <i class="fas fa-plus"></i> إضافة بند
                    </button>
                </div>

                <div style="overflow-x: auto; border-radius: 12px; border: 2px solid #e2e8f0;">
                    <table class="table mb-0" id="itemsTable">
                        <thead style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white;">
                            <tr>
                                <th style="padding: 1rem; text-align: center; width: 60px;">رقم</th>
                                <th style="padding: 1rem;">الوصف</th>
                                <th style="padding: 1rem; width: 180px;">المبلغ</th>
                                <th style="padding: 1rem; text-align: center; width: 70px;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <tr class="item-row">
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <span class="item-number" style="background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">1</span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="text" name="description[]" class="form-control" placeholder="مثال: Acer Nitro 5"
                                           style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="number" name="amount[]" class="form-control amount-input" placeholder="المبلغ (سالب = خصم)"
                                           step="0.01" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
                                </td>
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <button type="button" class="btn btn-sm remove-btn" onclick="removeItem(this)"
                                            style="background: #ef4444; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; visibility: hidden;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                                <td colspan="2" style="padding: 1.25rem; text-align: left; font-weight: 700; font-size: 1.2rem; color: #1e293b;">
                                    <i class="fas fa-money-bill-wave" style="color: #1e40af; margin-left: 0.5rem;"></i>
                                    الإجمالي الكلي:
                                </td>
                                <td colspan="2" style="padding: 1.25rem; font-weight: 700; font-size: 1.5rem; color: #1e40af;" id="grandTotal">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- الشروط والملاحظات -->
            <div class="service-card card-warning mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-sticky-note" style="color: #f59e0b;"></i>
                    الشروط والملاحظات
                </h5>
                <textarea name="notes" class="form-control" rows="4"
                          style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 1rem; resize: vertical;">{{ old('notes', "This financial claim is submitted for payment processing.\nPlease process the payment according to the agreed financial procedures.") }}</textarea>
            </div>

            <div class="text-center">
                <div class="d-inline-flex gap-3">
                    <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-save"></i>
                        <span>حفظ المطالبة</span>
                    </button>
                    <a href="{{ route('financial-claims.index') }}" class="btn btn-lg" style="background: #64748b; color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                        <i class="fas fa-times"></i>
                        <span>إلغاء</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemCount = 1;

    function addItem() {
        itemCount++;
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                <span class="item-number" style="background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">${itemCount}</span>
            </td>
            <td style="padding: 0.75rem;">
                <input type="text" name="description[]" class="form-control" placeholder="الوصف" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
            </td>
            <td style="padding: 0.75rem;">
                <input type="number" name="amount[]" class="form-control amount-input" placeholder="المبلغ" step="0.01" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
            </td>
            <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                <button type="button" class="btn btn-sm remove-btn" onclick="removeItem(this)" style="background: #ef4444; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        container.appendChild(newRow);
        updateItemNumbers();
        attachCalculationListeners();
    }

    function removeItem(btn) {
        btn.closest('.item-row').remove();
        updateItemNumbers();
        calculateTotal();
    }

    function updateItemNumbers() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            row.querySelector('.item-number').textContent = index + 1;
            row.querySelector('.remove-btn').style.visibility = index === 0 ? 'hidden' : 'visible';
        });
        itemCount = rows.length;
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.amount-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').textContent = total.toFixed(2);
    }

    function attachCalculationListeners() {
        document.querySelectorAll('.amount-input').forEach(input => {
            input.removeEventListener('input', calculateTotal);
            input.addEventListener('input', calculateTotal);
        });
    }

    attachCalculationListeners();

    document.querySelectorAll('input[name="language"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.lang-option').forEach(l => l.style.borderColor = '#e2e8f0');
            radio.closest('.lang-option').style.borderColor = '#0ea5e9';
        });
    });
    document.querySelector('input[name="language"]:checked').closest('.lang-option').style.borderColor = '#0ea5e9';
</script>
@endpush
@endsection
