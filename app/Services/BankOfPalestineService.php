<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankOfPalestineService
{
    protected string $merchantId;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        // القيم بتضل فاضية لحد ما توصل بيانات بنك فلسطين الحقيقية — لازم تبقى نوعها
        // string (مش null) حتى ما تنهار عملية الدفع كاملة بخطأ TypeError.
        $this->merchantId = (string) config('bankofpalestine.merchant_id');
        $this->secretKey  = (string) config('bankofpalestine.secret_key');
        $this->baseUrl    = (string) config('bankofpalestine.base_url');
    }

    /**
     * بيتحقق إذا بيانات بنك فلسطين الحقيقية موجودة — نقطة الفرق الوحيدة بين
     * مسار الدفع الحقيقي ومسار البوابة الوهمية المؤقتة.
     */
    public function credentialsConfigured(): bool
    {
        return !empty($this->merchantId) && !empty($this->secretKey);
    }

    /**
     * يبدأ عملية الدفع ويرجع رابط التحويل لصفحة بنك فلسطين.
     * ⚠️ حالياً Placeholder — بيتفعل فعلياً بعد ما توصلنا وثائق API الحقيقية من البنك.
     */
    public function initiatePayment(Order $order): string
    {
        if (! $this->credentialsConfigured()) {
            Log::warning('BankOfPalestine: merchant credentials not configured yet.');
            // مؤقتاً: نرجع لبوابة دفع وهمية محلية بدل بوابة حقيقية، حتى نقدر
            // نجرب مسار الدفع كامل (نجاح/فشل) قبل ما توصلنا بيانات البنك الحقيقية
            return route('bankofpalestine.mock', $order);
        }

        Payment::create([
            'order_id' => $order->id,
            'status'   => 'initiated',
            'amount'   => $order->total,
        ]);

        // ===== هيك رح يصير التنفيذ الفعلي لما توصلنا الوثائق =====
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $this->secretKey,
        // ])->post("{$this->baseUrl}/payments/initiate", [
        //     'merchant_id'  => $this->merchantId,
        //     'order_id'     => $order->id,
        //     'amount'       => $order->total,
        //     'currency'     => 'ILS',
        //     'callback_url' => config('bankofpalestine.callback_url'),
        //     'return_url'   => route('bankofpalestine.return', $order),
        // ]);
        //
        // return $response->json('payment_url');

        return route('bankofpalestine.return', $order);
    }

    /**
     * يتحقق من صحة توقيع الـ Callback الوارد من بنك فلسطين.
     * ⚠️ منطق التحقق الفعلي بيتحدد حسب وثائق البنك (HMAC عادة).
     */
    public function verifyCallbackSignature(array $payload, string $signature): bool
    {
        if (empty($this->secretKey)) {
            return false; // بدون مفتاح سري، ما منقبل أي Callback أبداً
        }

        $expected = hash_hmac('sha256', json_encode($payload), $this->secretKey);
        return hash_equals($expected, $signature);
    }

    public function handleCallback(array $payload): void
    {
        $order = Order::find($payload['order_id'] ?? null);
        if (!$order) {
            Log::error('BankOfPalestine callback: order not found', $payload);
            return;
        }

        $status = ($payload['status'] ?? '') === 'success' ? 'success' : 'failed';
        $alreadyResolved = in_array($order->status, ['paid', 'failed'], true);

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'transaction_id' => $payload['transaction_id'] ?? null,
                'status'         => $status,
                'amount'         => $payload['amount'] ?? $order->total,
                'raw_response'   => $payload,
            ]
        );

        $order->update(['status' => $status === 'success' ? 'paid' : 'failed']);

        if (! $alreadyResolved) {
            if ($status === 'failed') {
                $this->restoreStockFor($order);
            }

            $this->notifyCustomer($order, $status === 'success');
        }
    }

    private function notifyCustomer(Order $order, bool $success): void
    {
        $customer = $order->customer;
        if (! $customer) {
            return;
        }

        $notification = new \App\Notifications\OrderPaymentResultNotification($order, $success);

        if ($customer->email) {
            try {
                \Illuminate\Support\Facades\Notification::sendNow($customer, $notification, ['mail']);
            } catch (\Throwable $e) {
                Log::warning('OrderPaymentResultNotification mail failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        try {
            \Illuminate\Support\Facades\Notification::sendNow($customer, $notification, [\NotificationChannels\WebPush\WebPushChannel::class]);
        } catch (\Throwable $e) {
            Log::warning('OrderPaymentResultNotification webpush failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }

    private function restoreStockFor(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items()->lockForUpdate()->get() as $item) {
                if (!$item->product_id) {
                    continue;
                }

                $product = \App\Models\Product::where('id', $item->product_id)->lockForUpdate()->first();
                if (!$product) {
                    continue;
                }

                \App\Services\ProductStockService::increment($product, $item->quantity);
            }
        });
    }
}
