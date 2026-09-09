<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $cart = $customer->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'السلة فارغة');
        }

        return view('checkout.index', compact('cart', 'customer'));
    }

    public function store(Request $request)
{
    $customer = Auth::guard('customer')->user();
    $cart = $customer->cart()->with('items.product')->first();

    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'السلة فارغة');
    }

    $validated = $request->validate([
        'customer_name'    => 'required|string|max:255',
        'customer_phone'   => 'required|string|max:50',
        'shipping_address' => 'required|string|max:255',
        'shipping_city'    => 'nullable|string|max:255',
        'payment_method'   => 'required|in:jawwalpay,bankofpalestine,palpay',
    ]);

    $order = DB::transaction(function () use ($cart, $customer, $validated) {

        // نقفل كل منتج ونعيد فحص المخزون على القيمة الحقيقية اللحظية (مش القيمة اللي كانت
        // محمّلة قبل ما تبلش المعاملة) — منعاً لحالة سباق بين طلبين متزامنين على آخر قطعة.
        $lockedProducts = [];
        foreach ($cart->items as $item) {
            $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

            if (! $product || $product->is_out_of_stock || $product->quantity < $item->quantity) {
                $name = $product->name ?? $item->product->name ?? '';
                throw new \Exception("الكمية المتوفرة من \"{$name}\" غير كافية حالياً");
            }

            $lockedProducts[$item->id] = $product;
        }

        // الخصم لازم يُحسب من جديد هون، مش يوصل جاهز من الطلب — الكوبون المطبّق على السلة
        // بينحسب سيرفرياً (مش من أي قيمة جاي من الطلب نفسه) حتى ما يصير فيه تلاعب بمبلغ
        // الخصم قبل ما يوصل على initiatePayment().
        $appliedCoupon = $cart->applied_coupon;
        $discountAmount = $appliedCoupon ? $appliedCoupon->calculateDiscount($cart->total) : 0;
        $finalTotal = max(0, round($cart->total - $discountAmount, 2));

        $order = Order::create([
            'customer_id'      => $customer->id,
            'total'            => $finalTotal,
            'coupon_code'      => $appliedCoupon?->code,
            'discount_amount'  => $discountAmount,
            'status'           => 'pending',
            'payment_method'   => $validated['payment_method'],
            'customer_name'    => $validated['customer_name'],
            'customer_phone'   => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'shipping_city'    => $validated['shipping_city'] ?? null,
        ]);

        if ($appliedCoupon) {
            $appliedCoupon->increment('used_count');
        }

        foreach ($cart->items as $item) {
            $product = $lockedProducts[$item->id];

            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item->product_id,
                'branch_id'    => $product->branch_id,
                'product_name' => $product->name,
                'price'        => $item->price,
                'quantity'     => $item->quantity,
            ]);

            // خصم الكمية فعلياً من المخزون (عبر عنصر الكتالوج لو المنتج مربوط فيه،
            // حتى ما تنمسح هاي العملية لاحقاً لو تحدّث الكتالوج من نقطة البيع)
            \App\Services\ProductStockService::decrement($product, $item->quantity);

            // تحديث/إنشاء إشعار المخزون لهاد المنتج
            \App\Services\NotificationService::syncStock($product->fresh());
        }

        // تفريغ السلة وإلغاء الكوبون المطبّق بعد إنشاء الطلب
        $cart->items()->delete();
        $cart->update(['coupon_code' => null]);

        // إشعار طلب جديد
        \App\Services\NotificationService::notifyNewOrder($order);

        return $order;
    });

    // كل قناة إشعار بمحاولة منفصلة ومعزولة — Laravel بيوقف باقي القنوات لو
    // وحدة فشلت ضمن نفس نداء notify()، فلازم نفصلهم حتى فشل الإيميل (مثلاً
    // مشكلة اتصال SMTP) ما يمنع وصول إشعار الدفع الفوري (Push) وبالعكس.
    $orderForNotify = $order->load('items');

    if ($customer->email) {
        try {
            \Illuminate\Support\Facades\Notification::sendNow($customer, new \App\Notifications\OrderConfirmedNotification($orderForNotify), ['mail']);
        } catch (\Throwable $e) {
            // فشل إرسال إيميل التأكيد ما لازم يوقف عملية الطلب نفسها
            \Illuminate\Support\Facades\Log::warning('OrderConfirmedNotification mail failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }

    try {
        \Illuminate\Support\Facades\Notification::sendNow($customer, new \App\Notifications\OrderConfirmedNotification($orderForNotify), [\NotificationChannels\WebPush\WebPushChannel::class]);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('OrderConfirmedNotification webpush failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
    }

    $gatewayService = match ($order->payment_method) {
        'bankofpalestine' => \App\Services\BankOfPalestineService::class,
        'palpay'          => \App\Services\PalPayService::class,
        default           => \App\Services\JawwalPayService::class,
    };

    $paymentUrl = app($gatewayService)->initiatePayment($order);

    return redirect($paymentUrl);
}
}