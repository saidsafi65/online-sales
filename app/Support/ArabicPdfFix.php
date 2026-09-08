<?php

namespace App\Support;

/**
 * إصلاح خلل معروف بمكتبة dompdf: أي جزء عربي داخل نص RTL بيرجع معكوس بالكامل (حروف
 * الكلمة نفسها بالمقلوب، وترتيب الكلمات المتجاورة بالمقلوب كمان) — بينما الأجزاء
 * الإنجليزية/الأرقام جوا نفس النص بتضل صح ومكانها صح. تأكدنا من هالسلوك بالتجربة
 * الفعلية (PDF حقيقي، مو تخمين).
 *
 * الحل: نعكس مسبقاً بالضبط نفس الأجزاء العربية اللي رح يعكسها dompdf — فلما يعكسها
 * هو كمان، يرجعوا صح (عكس × عكس = الأصل). أي جزء إنجليزي/رقم/فاصل بيضل زي ما هو.
 */
class ArabicPdfFix
{
    public static function fix(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $chars = mb_str_split($text, 1, 'UTF-8');
        $count = count($chars);
        $result = [];
        $i = 0;

        while ($i < $count) {
            if (self::isArabic($chars[$i])) {
                // نمدّ الجزء العربي لأبعد نقطة ممكنة: نتخطى المسافات بس لو في عربي بعدها كمان،
                // حتى نجمع كلمتين عربيتين متجاورتين (متفصولات بمسافة) بنفس الجزء الواحد.
                $lastArabicIndex = $i;
                $j = $i + 1;
                while ($j < $count) {
                    if (self::isArabic($chars[$j])) {
                        $lastArabicIndex = $j;
                        $j++;
                        continue;
                    }
                    if ($chars[$j] === ' ') {
                        $k = $j;
                        while ($k < $count && $chars[$k] === ' ') {
                            $k++;
                        }
                        if ($k < $count && self::isArabic($chars[$k])) {
                            $j = $k;
                            continue;
                        }
                    }
                    break;
                }

                $run = array_slice($chars, $i, $lastArabicIndex - $i + 1);
                $result = array_merge($result, array_reverse($run));
                $i = $lastArabicIndex + 1;
            } else {
                $result[] = $chars[$i];
                $i++;
            }
        }

        return implode('', $result);
    }

    private static function isArabic(string $char): bool
    {
        $codepoint = mb_ord($char, 'UTF-8');
        if ($codepoint === false) {
            return false;
        }

        return ($codepoint >= 0x0600 && $codepoint <= 0x06FF)  // Arabic
            || ($codepoint >= 0x0750 && $codepoint <= 0x077F)  // Arabic Supplement
            || ($codepoint >= 0x08A0 && $codepoint <= 0x08FF)  // Arabic Extended-A
            || ($codepoint >= 0xFB50 && $codepoint <= 0xFDFF)  // Arabic Presentation Forms-A
            || ($codepoint >= 0xFE70 && $codepoint <= 0xFEFF); // Arabic Presentation Forms-B
    }
}
