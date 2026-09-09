@extends('layout.gust')

@section('title', 'إتمام الطلب')

@push('styles')
<style>
    .checkout-wrapper { padding: 1rem 0 3rem; }
    .checkout-title {
        font-size: 1.9rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 2rem;
    }
    .checkout-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.8rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 1.5rem;
    }
    .checkout-card h5 {
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 1.3rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .checkout-card h5 i { color: var(--primary-color); }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
        display: block;
        margin-bottom: .4rem;
        font-weight: 700;
        font-size: .9rem;
        color: var(--text-primary);
    }
    .form-control {
        border-radius: 10px;
        padding: .7rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 .2rem rgba(220,38,38,.15);
    }
    .order-line {
        display: flex;
        justify-content: space-between;
        padding: .6rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: .92rem;
    }
    .order-line:last-of-type { border-bottom: none; }
    .order-line-name { color: var(--text-primary); font-weight: 600; }
    .order-line-meta { color: var(--text-secondary); font-size: .82rem; }
    .order-subtotal-row {
        display: flex;
        justify-content: space-between;
        font-size: .95rem;
        color: var(--text-secondary);
        margin-top: .8rem;
    }
    .order-discount-row {
        display: flex;
        justify-content: space-between;
        font-size: .95rem;
        color: #059669;
        font-weight: 700;
        margin-top: .4rem;
    }
    .order-total-row {
        display: flex;
        justify-content: space-between;
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--text-primary);
        border-top: 1px solid #e2e8f0;
        padding-top: 1rem;
        margin-top: .8rem;
    }
    .payment-method-box {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        display: flex;
        align-items: center;
        gap: .8rem;
        margin-bottom: .8rem;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }
    .payment-method-box:hover { background: #f1f5f9; }
    .payment-method-box.selected { border-color: var(--primary-color); background: #fef2f2; }
    .payment-method-box i { font-size: 1.5rem; color: var(--primary-color); }
    .payment-method-box input[type="radio"] { width: 1.1rem; height: 1.1rem; accent-color: var(--primary-color); }
    .btn-place-order {
        width: 100%;
        padding: .95rem;
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.05rem;
    }
    .btn-place-order:hover { opacity: .92; color: #fff; }
    .summary-sticky { position: sticky; top: 100px; }
    @media (max-width: 576px) {
        .summary-sticky { position: static; top: auto; }
    }
</style>
@endpush

@section('content')
<div class="checkout-wrapper">
    <h1 class="checkout-title"><i class="fas fa-file-invoice"></i> إتمام الطلب</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-card">
                    <h5><i class="fas fa-map-marker-alt"></i> بيانات التوصيل</h5>

                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="customer_name" class="form-control"
                               value="{{ old('customer_name', $customer->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>رقم الهاتف</label>
                        <input type="text" name="customer_phone" class="form-control"
                               value="{{ old('customer_phone', $customer->phone) }}" required>
                    </div>

                    <div class="form-group">
                        <label>العنوان بالتفصيل</label>
                        <input type="text" name="shipping_address" class="form-control"
                               value="{{ old('shipping_address', $customer->address) }}" required
                               placeholder="اسم الشارع، رقم المبنى، أقرب معلم...">
                    </div>

                    <div class="form-group mb-0">
                        <label>المدينة</label>
                        <input type="text" name="shipping_city" class="form-control"
                               value="{{ old('shipping_city', $customer->city) }}">
                    </div>
                </div>

                <div class="checkout-card">
                    <h5><i class="fas fa-credit-card"></i> طريقة الدفع</h5>

                    <label class="payment-method-box" data-payment-option>
                        <input type="radio" name="payment_method" value="jawwalpay" checked>
                        <i class="fas fa-mobile-alt"></i>
                        <div>
                            <div class="fw-bold">جوال باي</div>
                            <div class="text-secondary" style="font-size:.85rem;">
                                رح تنتقل لصفحة جوال باي لإتمام الدفع بأمان
                            </div>
                        </div>
                    </label>

                    <label class="payment-method-box" data-payment-option>
                        <input type="radio" name="payment_method" value="bankofpalestine">
                        <i class="fas fa-landmark"></i>
                        <div>
                            <div class="fw-bold">بنك فلسطين</div>
                            <div class="text-secondary" style="font-size:.85rem;">
                                رح تنتقل لصفحة بنك فلسطين لإتمام الدفع بأمان
                            </div>
                        </div>
                    </label>

                    <label class="payment-method-box" data-payment-option>
                        <input type="radio" name="payment_method" value="palpay">
                        <i class="fas fa-wallet"></i>
                        <div>
                            <div class="fw-bold">محفظة بال باي</div>
                            <div class="text-secondary" style="font-size:.85rem;">
                                رح تنتقل لتطبيق بال باي لإتمام الدفع بأمان
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-card summary-sticky">
                    <h5><i class="fas fa-receipt"></i> ملخص الطلب</h5>

                    @foreach ($cart->items as $item)
                        <div class="order-line">
                            <div>
                                <div class="order-line-name">{{ $item->product->name }}</div>
                                <div class="order-line-meta">{{ $item->quantity }} × {{ number_format($item->price, 2) }} ₪</div>
                            </div>
                            <div class="fw-bold">{{ number_format($item->subtotal, 2) }} ₪</div>
                        </div>
                    @endforeach

                    <div class="order-subtotal-row">
                        <span>المجموع الفرعي</span>
                        <span>{{ number_format($cart->total, 2) }} ₪</span>
                    </div>
                    @if ($cart->discount > 0)
                        <div class="order-discount-row">
                            <span>الخصم ({{ $cart->coupon_code }})</span>
                            <span>- {{ number_format($cart->discount, 2) }} ₪</span>
                        </div>
                    @endif
                    <div class="order-total-row">
                        <span>الإجمالي</span>
                        <span>{{ number_format($cart->grand_total, 2) }} ₪</span>
                    </div>

                    <button type="submit" class="btn-place-order mt-3">
                        <i class="fas fa-lock"></i> تأكيد الطلب والدفع
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    (function () {
        function syncSelected() {
            document.querySelectorAll('[data-payment-option]').forEach(function (box) {
                const input = box.querySelector('input[type="radio"]');
                box.classList.toggle('selected', input.checked);
            });
        }
        document.querySelectorAll('[data-payment-option] input[type="radio"]').forEach(function (input) {
            input.addEventListener('change', syncSelected);
        });
        syncSelected();
    })();
</script>
@endsection