<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PalPayService
{
    protected string $merchantId;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        // البيانات بتنقرا من إعدادات المعرض (Tenant) نفسه، مش من .env — كل معرض
        // إله حساب بال باي خاص فيه، بيتحكم فيه من صفحة "طرق الدفع". القيم بتضل
        // فاضية لحد ما يحطها صاحب المعرض — لازم تبقى string (مش null) حتى ما تنهار
        // عملية الدفع كاملة بخطأ TypeError.
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;
        $this->merchantId = (string) ($tenant->palpay_merchant_id ?? '');
        $this->secretKey  = (string) ($tenant->palpay_secret_key ?? '');
        $this->baseUrl    = (string) ($tenant->palpay_base_url ?? '');
    }

    /**
     * بيتحقق إذا بيانات بال باي الحقيقية موجودة — نقطة الفرق الوحيدة بين
     * مسار الدفع الحقيقي ومسار البوابة الوهمية المؤقتة.
     */
    public function credentialsConfigured(): bool
    {
        return !empty($this->merchantId) && !empty($this->secretKey);
    }

    /**
     * يبدأ عملية الدفع ويرجع رابط التحويل لصفحة بال باي.
     * ⚠️ حالياً Placeholder — بيتفعل فعلياً بعد ما توصلنا وثائق API الحقيقية من بال باي.
     */
    public function initiatePayment(Order $order): string
    {
        if (! $this->credentialsConfigured()) {
            Log::warning('PalPay: merchant credentials not configured yet.');
            // مؤقتاً: نرجع لبوابة دفع وهمية محلية بدل بوابة حقيقية، حتى نقدر
            // نجرب مسار الدفع كامل (نجاح/فشل) قبل ما توصلنا بيانات بال باي الحقيقية
            return route('palpay.mock', $order);
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
        //     'callback_url' => route('palpay.callback'),
        //     'return_url'   => route('palpay.return', $order),
        // ]);
        //
        // return $response->json('payment_url');

        return route('palpay.return', $order);
    }

    /**
     * يتحقق من صحة توقيع الـ Callback الوارد من بال باي.
     * ⚠️ منطق التحقق الفعلي بيتحدد حسب وثائق بال باي (HMAC عادة).
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
            Log::error('PalPay callback: order not found', $payload);
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
