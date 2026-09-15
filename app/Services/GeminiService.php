<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;

    // "latest" بدل رقم نسخة ثابت — عشان ما تنكسر لما Google تسحب نسخة قديمة (صار هيك فعلاً مع gemini-2.0-flash)
    private const MODEL = 'gemini-flash-latest';
    private const SYSTEM_INSTRUCTION = <<<'TEXT'
أنت مساعد ذكي لموظفي محل صيانة وبيع أجهزة (هواتف، لابتوبات، إلكترونيات). جاوب بالعربي بشكل مختصر ومفيد.
إذا سُئلت عن مواصفات قطعة غيار دقيقة (نوع الكونكتور، عدد الأطراف، رقم القطعة الأصلي) لجهاز معيّن، أعط أفضل إجابة
عندك، لكن ذكّر المستخدم مرة وحدة بنهاية الرد إنه لازم يتأكد من الرقم الفعلي قبل ما يطلب القطعة، لأن هالتفاصيل
ممكن تختلف حسب نسخة إنتاج الجهاز.
TEXT;

    public function __construct()
    {
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;
        $this->apiKey = (string) ($tenant->gemini_api_key ?? '');
    }

    public function credentialsConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    /**
     * بيرسل رسالة جديدة + آخر رسائل المحادثة كسياق، ويرجّع رد المساعد.
     *
     * @param  array<int, array{role: string, content: string}>  $recentHistory  الأقدم أولاً
     * @return array{success: bool, reply?: string, message?: string}
     */
    public function reply(array $recentHistory, string $newMessage): array
    {
        if (! $this->credentialsConfigured()) {
            return [
                'success' => false,
                'message' => 'لسا ما انضاف مفتاح Gemini — أضفه من إعدادات "خدمات Google" (تحتاج صلاحية المدير).',
            ];
        }

        $contents = [];
        foreach ($recentHistory as $message) {
            $contents[] = [
                'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $message['content']]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $newMessage]]];

        // Gemini's free tier يرجّع 503 "high demand" أو تايم آوت شبكة أحياناً بشكل عابر —
        // Google نفسها بتنصح بإعادة محاولة قصيرة بدل ما نفشل من أول مرة.
        $maxAttempts = 3;
        $lastMessage = 'تعذّر الاتصال بالمساعد الذكي حالياً، تأكد من صحة بيانات الإعدادات.';

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(15)
                    ->withHeaders(['X-goog-api-key' => $this->apiKey])
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/'.self::MODEL.':generateContent', [
                        'contents' => $contents,
                        'systemInstruction' => ['parts' => [['text' => self::SYSTEM_INSTRUCTION]]],
                        'generationConfig' => ['maxOutputTokens' => 1024, 'temperature' => 0.7],
                    ]);

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');

                    if (! $text) {
                        return ['success' => false, 'message' => 'ما وصل رد من المساعد، جرب مرة ثانية.'];
                    }

                    return ['success' => true, 'reply' => $text];
                }

                Log::warning('GeminiService: request failed', [
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->status() === 429) {
                    // حد الحصة اليومية — إعادة المحاولة فوراً ما رح تفيد.
                    return ['success' => false, 'message' => 'تم الوصول للحد اليومي المجاني للمساعد الذكي. جرب مرة ثانية بكرا.'];
                }

                if ($response->status() === 503) {
                    $lastMessage = 'المساعد الذكي مشغول حالياً (ضغط طلبات على خدمة Google)، جرب كمان شوي.';
                    if ($attempt < $maxAttempts) {
                        usleep(400_000 * $attempt);
                        continue;
                    }
                }

                return ['success' => false, 'message' => $lastMessage];
            } catch (\Throwable $e) {
                Log::error('GeminiService: reply failed', ['attempt' => $attempt, 'error' => $e->getMessage()]);
                $lastMessage = 'حدث خطأ أثناء التواصل مع المساعد الذكي، جرب مرة ثانية.';

                if ($attempt < $maxAttempts) {
                    usleep(400_000 * $attempt);
                    continue;
                }
            }
        }

        return ['success' => false, 'message' => $lastMessage];
    }
}
