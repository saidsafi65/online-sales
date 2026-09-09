<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $senderId;
    protected string $baseUrl;

    public function __construct()
    {
        // القيم بتضل فاضية لحد ما توصل بيانات شركة الـ SMS الحقيقية (HotSMS أو غيرها).
        $this->apiKey    = (string) config('sms.api_key');
        $this->apiSecret = (string) config('sms.api_secret');
        $this->senderId  = (string) config('sms.sender_id');
        $this->baseUrl   = (string) config('sms.base_url');
    }

    /**
     * بيتحقق إذا بيانات شركة الـ SMS الحقيقية موجودة — نقطة الفرق الوحيدة بين
     * الإرسال الحقيقي والوضع الوهمي المؤقت (بيسجل الرسالة بس، ما بيبعتها فعلياً).
     */
    public function credentialsConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->apiSecret) && !empty($this->baseUrl);
    }

    /**
     * يبعت رسالة SMS. بيرجع true/false حسب نجاح الإرسال (أو التسجيل الوهمي).
     * ⚠️ ما بيرمي استثناء أبداً — فشل إرسال SMS ما لازم يكسر العملية الأساسية
     * (مثلاً إضافة الصيانة) اللي استدعته.
     */
    public function send(string $phone, string $message, ?string $purpose = null): bool
    {
        if (! $this->credentialsConfigured()) {
            return $this->mockSend($phone, $message, $purpose);
        }

        try {
            // ===== هيك رح يصير الإرسال الفعلي لما توصلنا وثائق API الشركة الحقيقية =====
            // (الشكل بيختلف شوي حسب الشركة يلي رح تنختار — HotSMS أو 4jawaly أو غيرها،
            // هاد بس هيكل تقريبي شائع لبوابات الـ SMS بالمنطقة)
            // $response = Http::asForm()->post($this->baseUrl, [
            //     'api_key'    => $this->apiKey,
            //     'api_secret' => $this->apiSecret,
            //     'sender'     => $this->senderId,
            //     'to'         => $phone,
            //     'message'    => $message,
            // ]);
            //
            // SmsLog::create([
            //     'phone' => $phone,
            //     'message' => $message,
            //     'purpose' => $purpose,
            //     'status' => $response->successful() ? 'sent' : 'failed',
            //     'provider_response' => $response->body(),
            // ]);
            //
            // return $response->successful();

            Log::warning('SmsService: real credentials configured but the API call is not wired in yet — falling back to mock.');
            return $this->mockSend($phone, $message, $purpose);
        } catch (\Throwable $e) {
            Log::error('SmsService: send failed', ['phone' => $phone, 'error' => $e->getMessage()]);
            SmsLog::create([
                'phone' => $phone,
                'message' => $message,
                'purpose' => $purpose,
                'status' => 'failed',
                'provider_response' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function mockSend(string $phone, string $message, ?string $purpose): bool
    {
        Log::info('SMS (وضع تجريبي — ما انبعتت فعلياً، لسا ما وصلت بيانات شركة حقيقية)', [
            'to' => $phone,
            'message' => $message,
        ]);

        SmsLog::create([
            'phone' => $phone,
            'message' => $message,
            'purpose' => $purpose,
            'status' => 'mock',
        ]);

        return true;
    }
}
