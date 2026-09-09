@extends('layout.app')

@section('title', 'إضافة عرض سعر')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">💰 إضافة عرض سعر</h1>
    <p class="welcome-subtitle">Price Quote — عرض سعر بالعربي أو الإنجليزي</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-11 col-xl-10">
        <form method="POST" action="{{ route('price-quotes.store') }}" id="quoteForm">
            @csrf

            <!-- اللغة -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-language" style="color: #0ea5e9;"></i>
                    لغة المستند
                </h5>
                <div class="d-flex gap-3">
                    <label style="flex:1; cursor:pointer; border: 2px solid #0ea5e9; border-radius: 12px; padding: 1rem; display:flex; align-items:center; gap:0.6rem;" class="lang-option">
                        <input type="radio" name="language" value="ar" checked style="width:1.2rem;height:1.2rem;">
                        <span style="font-weight:600;">🇵🇸 عربي</span>
                    </label>
                    <label style="flex:1; cursor:pointer; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem; display:flex; align-items:center; gap:0.6rem;" class="lang-option">
                        <input type="radio" name="language" value="en" style="width:1.2rem;height:1.2rem;">
                        <span style="font-weight:600;">🇬🇧 English</span>
                    </label>
                </div>
            </div>

            <!-- معلومات العرض -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #0ea5e9;"></i>
                    معلومات عرض السعر
                </h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">رقم العرض</label>
                        <input type="text" class="form-control" value="{{ $quoteNumber }}" disabled
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0; background:#f8fafc;">
                        <div class="form-text">بيتحدد تلقائياً حسب تاريخ العرض عند الحفظ</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">التاريخ</label>
                        <input type="date" name="quote_date" class="form-control @error('quote_date') is-invalid @enderror"
                               value="{{ old('quote_date', date('Y-m-d')) }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                        @error('quote_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">صالح حتى (اختياري)</label>
                        <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
                               value="{{ old('valid_until') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600;">العملة</label>
                        <input type="text" name="currency" class="form-control" value="{{ old('currency', 'ILS') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                    </div>
                </div>
            </div>

            <!-- بيانات العميل -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-user" style="color: #0ea5e9;"></i>
                    بيانات العميل
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-weight: 600;">اسم العميل / الجهة</label>
                        <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror"
                               value="{{ old('client_name') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                        @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-weight: 600;">الهاتف (اختياري)</label>
                        <input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-weight: 600;">الإيميل (اختياري)</label>
                        <input type="email" name="client_email" class="form-control" value="{{ old('client_email') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                </div>
            </div>

            <!-- بنود العرض -->
            <div class="service-card card-success mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-box" style="color: #10b981;"></i>
                        بنود العرض
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
                                <th style="padding: 1rem; width: 120px;">الكمية</th>
                                <th style="padding: 1rem; width: 130px;">السعر</th>
                                <th style="padding: 1rem; width: 130px;">الإجمالي</th>
                                <th style="padding: 1rem; text-align: center; width: 70px;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <tr class="item-row">
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <span class="item-number" style="background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">1</span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="text" name="description[]" class="form-control" placeholder="وصف المنتج/الخدمة"
                                           style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="الكمية" step="1" value="1" min="1"
                                           style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="number" name="price[]" class="form-control price-input" placeholder="السعر" step="0.01"
                                           style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="text" class="form-control item-total" placeholder="0.00" readonly
                                           style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem; font-weight: 600; color: #10b981;">
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
                            <tr style="background: #f8fafc;">
                                <td colspan="4" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 1.1rem; color: #1e293b;">
                                    الإجمالي قبل الخصم:
                                </td>
                                <td colspan="2" style="padding: 1rem; font-weight: 700; font-size: 1.3rem; color: #64748b;" id="subtotal">0.00</td>
                            </tr>
                            <tr style="background: #fef3c7;">
                                <td colspan="4" style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <label style="font-weight: 600; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                            <i class="fas fa-percentage" style="color: #f59e0b;"></i>
                                            مبلغ الخصم:
                                        </label>
                                        <input type="number" name="discount_amount" id="discountInput" class="form-control"
                                               value="{{ old('discount_amount', 0) }}" step="0.01" placeholder="0.00"
                                               style="max-width: 200px; width: 100%; border: 2px solid #fbbf24; border-radius: 8px; padding: 0.6rem; font-weight: 600;">
                                    </div>
                                </td>
                                <td colspan="2" style="padding: 1rem; font-weight: 700; font-size: 1.2rem; color: #f59e0b;" id="discountDisplay">0.00</td>
                            </tr>
                            <tr style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                                <td colspan="4" style="padding: 1.25rem; text-align: left; font-weight: 700; font-size: 1.2rem; color: #1e293b;">
                                    <i class="fas fa-money-bill-wave" style="color: #10b981; margin-left: 0.5rem;"></i>
                                    الإجمالي النهائي:
                                </td>
                                <td colspan="2" style="padding: 1.25rem; font-weight: 700; font-size: 1.5rem; color: #10b981;" id="grandTotal">0.00</td>
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
                <textarea name="notes" class="form-control" rows="4" placeholder="أي شروط أو ملاحظات إضافية..."
                          style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 1rem; resize: vertical;">{{ old('notes') }}</textarea>
            </div>

            <div class="text-center">
                <div class="d-inline-flex gap-3">
                    <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-save"></i>
                        <span>حفظ عرض السعر</span>
                    </button>
                    <a href="{{ route('price-quotes.index') }}" class="btn btn-lg" style="background: #64748b; color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
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
                <input type="text" name="description[]" class="form-control" placeholder="وصف المنتج/الخدمة" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
            </td>
            <td style="padding: 0.75rem;">
                <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="الكمية" step="1" value="1" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
            </td>
            <td style="padding: 0.75rem;">
                <input type="number" name="price[]" class="form-control price-input" placeholder="السعر" step="0.01" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
            </td>
            <td style="padding: 0.75rem;">
                <input type="text" class="form-control item-total" placeholder="0.00" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem; font-weight: 600; color: #10b981;" readonly>
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
        calculateTotals();
    }

    function updateItemNumbers() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            row.querySelector('.item-number').textContent = index + 1;
            row.querySelector('.remove-btn').style.visibility = index === 0 ? 'hidden' : 'visible';
        });
        itemCount = rows.length;
    }

    function calculateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        row.querySelector('.item-total').value = (quantity * price).toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            subtotal += parseFloat(row.querySelector('.item-total').value) || 0;
        });
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const grandTotal = subtotal - discount;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('discountDisplay').textContent = discount.toFixed(2);
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
    }

    function attachCalculationListeners() {
        document.querySelectorAll('.item-row').forEach(row => {
            row.querySelector('.quantity-input').addEventListener('input', () => calculateRowTotal(row));
            row.querySelector('.price-input').addEventListener('input', () => calculateRowTotal(row));
        });
        document.getElementById('discountInput').addEventListener('input', calculateTotals);
    }

    attachCalculationListeners();

    document.querySelectorAll('input[name="language"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.lang-option').forEach(l => l.style.borderColor = '#e2e8f0');
            radio.closest('.lang-option').style.borderColor = '#0ea5e9';
        });
    });
</script>
@endpush
@endsection
