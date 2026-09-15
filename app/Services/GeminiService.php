<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;

    private const MODEL = 'gemini-2.0-flash';
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

        try {
            $response = Http::timeout(20)
                ->withHeaders(['X-goog-api-key' => $this->apiKey])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/'.self::MODEL.':generateContent', [
                    'contents' => $contents,
                    'systemInstruction' => ['parts' => [['text' => self::SYSTEM_INSTRUCTION]]],
                    'generationConfig' => ['maxOutputTokens' => 1024, 'temperature' => 0.7],
                ]);

            if (! $response->successful()) {
                Log::warning('GeminiService: request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->status() === 429) {
                    return ['success' => false, 'message' => 'تم الوصول للحد اليومي المجاني للمساعد الذكي. جرب مرة ثانية بكرا.'];
                }

                return ['success' => false, 'message' => 'تعذّر الاتصال بالمساعد الذكي حالياً، تأكد من صحة بيانات الإعدادات.'];
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (! $text) {
                return ['success' => false, 'message' => 'ما وصل رد من المساعد، جرب مرة ثانية.'];
            }

            return ['success' => true, 'reply' => $text];
        } catch (\Throwable $e) {
            Log::error('GeminiService: reply failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'message' => 'حدث خطأ أثناء التواصل مع المساعد الذكي.'];
        }
    }
}
