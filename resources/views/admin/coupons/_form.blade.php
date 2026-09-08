@php $coupon = $coupon ?? null; @endphp

<div class="mb-3">
    <label class="form-label fw-bold">الكود</label>
    <input type="text" name="code" class="form-control" style="font-family: monospace; text-transform: uppercase;"
        value="{{ old('code', $coupon->code ?? '') }}" placeholder="مثال: WELCOME10" maxlength="40" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">نوع الخصم</label>
        <select name="type" class="form-select" required>
            <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected' : '' }}>نسبة مئوية %</option>
            <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت (شيكل)</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">القيمة</label>
        <input type="number" name="value" class="form-control" step="0.01" min="0.01"
            value="{{ old('value', $coupon->value ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">الحد الأدنى للطلب (اختياري)</label>
        <input type="number" name="min_order_amount" class="form-control" step="0.01" min="0"
            value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}" placeholder="بدون حد أدنى">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">الحد الأقصى لعدد مرات الاستخدام (اختياري)</label>
        <input type="number" name="max_uses" class="form-control" min="1"
            value="{{ old('max_uses', $coupon->max_uses ?? '') }}" placeholder="بدون حد أقصى">
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">تاريخ الانتهاء (اختياري)</label>
    <input type="date" name="expires_at" class="form-control"
        value="{{ old('expires_at', optional($coupon?->expires_at)->format('Y-m-d')) }}">
</div>

<div class="form-check form-switch">
    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
        {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="form-check-label fw-bold">مفعّل</label>
</div>
