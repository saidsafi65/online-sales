<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * تنبيه فوري بالإيميل عند حصول خطأ حقيقي غير متوقع بالموقع الحي، بدل ما يكتشفه
 * حدا بالصدفة بعد فترة طويلة. محدود بعدد مرات (لنفس الخطأ ولإجمالي الساعة) حتى
 * ما يغرقنا بإيميلات لو صار خلل متكرر بسرعة.
 */
class ErrorAlertMailer
{
    private const DEDUP_TTL_MINUTES = 30;

    private const MAX_PER_HOUR = 20;

    public function maybeSend(Throwable $e): void
    {
        try {
            $to = config('services.error_alert.email');
            if (! $to) {
                return;
            }

            $dedupKey = 'error_alert:dedup:'.md5(get_class($e).'|'.$e->getMessage().'|'.$e->getFile().'|'.$e->getLine());
            if (Cache::has($dedupKey)) {
                return;
            }

            $hourKey = 'error_alert:count:'.now()->format('Y-m-d-H');
            $count = (int) Cache::get($hourKey, 0);
            if ($count >= self::MAX_PER_HOUR) {
                return;
            }

            Cache::put($dedupKey, true, now()->addMinutes(self::DEDUP_TTL_MINUTES));
            Cache::put($hourKey, $count + 1, now()->addHour());

            $tenant = app()->bound('currentTenant') ? app('currentTenant')->name : 'غير معروف';
            $body = $this->format($e, $tenant);

            Mail::raw($body, function ($message) use ($to, $e) {
                $message->to($to)
                    ->subject('⚠️ خطأ بالموقع: '.class_basename($e).' — '.mb_substr($e->getMessage(), 0, 80));
            });
        } catch (Throwable $mailError) {
            // مهم: أي فشل بإرسال التنبيه نفسه لازم يُبلع هون — ما بدنا نكسر معالجة
            // الأخطاء الأصلية أو نسبب حلقة أخطاء لا نهائية.
            Log::warning('ErrorAlertMailer failed to send alert', ['error' => $mailError->getMessage()]);
        }
    }

    private function format(Throwable $e, string $tenant): string
    {
        $url = 'console/CLI';
        try {
            $url = request()->fullUrl();
        } catch (Throwable) {
            // خارج سياق طلب HTTP (أمر Artisan مثلاً) — نتركها كما هي
        }

        return implode("\n", [
            'معرض: '.$tenant,
            'الرابط: '.$url,
            'الوقت: '.now()->format('Y-m-d H:i:s'),
            'النوع: '.get_class($e),
            'الرسالة: '.$e->getMessage(),
            'الملف: '.$e->getFile().':'.$e->getLine(),
            '',
            'أول 15 سطر من الـ trace:',
            implode("\n", array_slice(explode("\n", $e->getTraceAsString()), 0, 15)),
        ]);
    }
}
