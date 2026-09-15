<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSearchService
{
    protected string $apiKey;
    protected string $cx;

    public function __construct()
    {
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;
        $this->apiKey = (string) ($tenant->google_search_api_key ?? '');
        $this->cx = (string) ($tenant->google_search_cx ?? '');
    }

    public function credentialsConfigured(): bool
    {
        return $this->apiKey !== '' && $this->cx !== '';
    }

    /**
     * بيبحث عن الاستعلام بالإنترنت عبر Google Custom Search، ويرجّع نتائج جاهزة
     * للعرض للموظف (عنوان/رابط/مقتطف) — الموظف هو يللي يتأكد منها ويدخل
     * المواصفات يدوياً، هاد مش تعبئة تلقائية للحقول.
     *
     * @return array{success: bool, results?: array, message?: string}
     */
    public function search(string $query): array
    {
        if (! $this->credentialsConfigured()) {
            return [
                'success' => false,
                'message' => 'لسا ما انضاف مفتاح بحث Google — أضفه من إعدادات "بحث الإنترنت" (تحتاج صلاحية المدير).',
            ];
        }

        try {
            $response = Http::timeout(10)->get('https://www.googleapis.com/customsearch/v1', [
                'key' => $this->apiKey,
                'cx' => $this->cx,
                'q' => trim($query).' specifications',
                'num' => 6,
            ]);

            if (! $response->successful()) {
                Log::warning('GoogleSearchService: request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->status() === 429) {
                    return ['success' => false, 'message' => 'تم الوصول للحد اليومي المجاني للبحث. جرب مرة ثانية بكرا.'];
                }

                return ['success' => false, 'message' => 'تعذّر الاتصال بخدمة البحث حالياً، تأكد من صحة بيانات الإعدادات.'];
            }

            $items = $response->json('items', []);

            $results = array_map(fn (array $item) => [
                'title' => $item['title'] ?? '',
                'link' => $item['link'] ?? '',
                'snippet' => $item['snippet'] ?? '',
            ], $items);

            if (empty($results)) {
                return ['success' => true, 'results' => [], 'message' => 'ما في نتائج مطابقة'];
            }

            return ['success' => true, 'results' => $results];
        } catch (\Throwable $e) {
            Log::error('GoogleSearchService: search failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'message' => 'حدث خطأ أثناء البحث، حاول مرة ثانية.'];
        }
    }
}
