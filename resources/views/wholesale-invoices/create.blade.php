@extends('layout.app')

@section('title', 'إضافة فاتورة جملة جديدة')

@push('styles')
<style>
    @media (max-width: 768px) {
        #itemsTable {
            font-size: 0.85rem;
        }

        #itemsTable th,
        #itemsTable td {
            padding: 0.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">📦 إضافة فاتورة بيع بالجملة</h1>
    <p class="welcome-subtitle">فاتورة بيع بين معرضك ومحل/معرض تاني (B2B)</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-11 col-xl-10">
        <form method="POST" action="{{ route('wholesale-invoices.store') }}" id="invoiceForm">
            @csrf

            <!-- بيانات المحل المشتري -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-store" style="color: #b91c1c;"></i>
                    بيانات المحل / المعرض المشتري
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-store"></i> اسم المحل / المعرض
                        </label>
                        <input type="text" name="buyer_store_name" class="form-control @error('buyer_store_name') is-invalid @enderror"
                               value="{{ old('buyer_store_name') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                        @error('buyer_store_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-id-card"></i> الرقم الضريبي (اختياري)
                        </label>
                        <input type="text" name="buyer_tax_number" class="form-control"
                               value="{{ old('buyer_tax_number') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-phone"></i> رقم الهاتف (اختياري)
                        </label>
                        <input type="text" name="buyer_phone" class="form-control"
                               value="{{ old('buyer_phone') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-map-marker-alt"></i> العنوان (اختياري)
                        </label>
                        <input type="text" name="buyer_address" class="form-control"
                               value="{{ old('buyer_address') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                </div>
            </div>

            <!-- معلومات الفاتورة -->
            <div class="service-card card-primary mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #b91c1c;"></i>
                    معلومات الفاتورة
                </h5>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-calendar"></i> التاريخ
                        </label>
                        <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
                               value="{{ old('invoice_date', date('Y-m-d')) }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                        @error('invoice_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-hashtag"></i> رقم الفاتورة
                        </label>
                        <input type="text" name="invoice_number" class="form-control @error('invoice_number') is-invalid @enderror"
                               value="{{ old('invoice_number', $invoiceNumber) }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-hand-holding-usd"></i> شروط الدفع
                        </label>
                        <select name="payment_terms" id="paymentTermsSelect" class="form-control @error('payment_terms') is-invalid @enderror"
                                style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;" required>
                            <option value="cash" {{ old('payment_terms') === 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="credit" {{ old('payment_terms') === 'credit' ? 'selected' : '' }}>آجل</option>
                        </select>
                        @error('payment_terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3" id="dueDateWrap" style="{{ old('payment_terms') === 'credit' ? '' : 'display:none;' }}">
                        <label class="form-label" style="font-weight: 600; color: #1e293b;">
                            <i class="fas fa-calendar-check"></i> تاريخ الاستحقاق
                        </label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date') }}"
                               style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid #e2e8f0;">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- قسم المنتجات -->
            <div class="service-card card-success mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-box" style="color: #10b981;"></i>
                        المنتجات
                    </h5>
                    <button type="button" class="btn" onclick="addItem()" style="background: #10b981; color: white; padding: 0.6rem 1.5rem; border-radius: 10px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus"></i>
                        إضافة منتج
                    </button>
                </div>

                <div style="overflow-x: auto; border-radius: 12px; border: 2px solid #e2e8f0;">
                    <table class="table mb-0" id="itemsTable">
                        <thead style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white;">
                            <tr>
                                <th style="padding: 1rem; text-align: center; width: 60px;">رقم</th>
                                <th style="padding: 1rem;">وصف المنتج</th>
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
                                    <input type="text" name="description[]" class="form-control" placeholder="وصف المنتج"
                                           style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="الكمية" step="1" value="1" min="1"
                                           style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <input type="number" name="price[]" class="form-control price-input" placeholder="سعر الجملة" step="0.01"
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
                                <td colspan="2" style="padding: 1rem; font-weight: 700; font-size: 1.3rem; color: #64748b;" id="subtotal">
                                    0.00 شيكل
                                </td>
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
                                <td colspan="2" style="padding: 1rem; font-weight: 700; font-size: 1.2rem; color: #f59e0b;" id="discountDisplay">
                                    0.00 شيكل
                                </td>
                            </tr>
                            <tr style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                                <td colspan="4" style="padding: 1.25rem; text-align: left; font-weight: 700; font-size: 1.2rem; color: #1e293b;">
                                    <i class="fas fa-money-bill-wave" style="color: #10b981; margin-left: 0.5rem;"></i>
                                    الإجمالي النهائي:
                                </td>
                                <td colspan="2" style="padding: 1.25rem; font-weight: 700; font-size: 1.5rem; color: #10b981;" id="grandTotal">
                                    0.00 شيكل
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- الملاحظات -->
            <div class="service-card card-warning mb-4">
                <h5 style="color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-sticky-note" style="color: #f59e0b;"></i>
                    ملاحظات
                </h5>
                <textarea name="notes" class="form-control" rows="4" placeholder="أي ملاحظات إضافية..."
                          style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 1rem; font-size: 1rem; resize: vertical;">{{ old('notes') }}</textarea>
            </div>

            <!-- أزرار الحفظ -->
            <div class="text-center">
                <div class="d-inline-flex gap-3">
                    <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-save"></i>
                        <span>حفظ الفاتورة</span>
                    </button>
                    <a href="{{ route('wholesale-invoices.index') }}" class="btn btn-lg" style="background: #64748b; color: white; padding: 12px 40px; border-radius: 50px; border: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
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

    document.getElementById('paymentTermsSelect').addEventListener('change', function () {
        document.getElementById('dueDateWrap').style.display = this.value === 'credit' ? '' : 'none';
    });

    function addItem() {
        itemCount++;
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.style.animation = 'fadeIn 0.3s ease-out';
        newRow.innerHTML = `
            <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                <span class="item-number" style="background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">${itemCount}</span>
            </td>
            <td style="padding: 0.75rem;">
                <input type="text" name="description[]" class="form-control" placeholder="وصف المنتج" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
            </td>
            <td style="padding: 0.75rem;">
                <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="الكمية" step="1" value="1" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
            </td>
            <td style="padding: 0.75rem;">
                <input type="number" name="price[]" class="form-control price-input" placeholder="سعر الجملة" step="0.01" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem;" required>
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
        const row = btn.closest('.item-row');
        row.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            row.remove();
            updateItemNumbers();
            calculateTotals();
        }, 300);
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
        const total = quantity * price;
        row.querySelector('.item-total').value = total.toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            subtotal += parseFloat(row.querySelector('.item-total').value) || 0;
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const grandTotal = subtotal - discount;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' شيكل';
        document.getElementById('discountDisplay').textContent = discount.toFixed(2) + ' شيكل';
        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2) + ' شيكل';
    }

    function attachCalculationListeners() {
        document.querySelectorAll('.item-row').forEach(row => {
            const qtyInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            qtyInput.addEventListener('input', () => calculateRowTotal(row));
            priceInput.addEventListener('input', () => calculateRowTotal(row));
        });

        document.getElementById('discountInput').addEventListener('input', calculateTotals);
    }

    attachCalculationListeners();
</script>
@endpush

@push('styles')
<style>
    .form-control:focus {
        border-color: #b91c1c !important;
        box-shadow: 0 0 0 0.25rem rgba(30, 64, 175, 0.15) !important;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }

    .table tbody tr { transition: all 0.2s ease; }
    .table tbody tr:hover { background: #f8fafc; }

    #discountInput:focus {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.15) !important;
    }
</style>
@endpush
@endsection
