<?php

namespace App\Support;

class SmsTextBuilder
{
    /**
     * يبني نص رسالة SMS ويضمن إنه يضل ضمن حد الرسالة الوحدة — رسالة SMS عربية بتتحول
     * تلقائياً لتشفير UCS-2 (لوجود حروف عربية)، وحدها الأقصى لرسالة وحدة 70 حرف بس
     * (مش 160 متل الإنجليزي)؛ لو تعدّت، بتنكسر لرسالتين أو أكتر وتتضاعف التكلفة.
     * لو النص طلع أطول من اللازم، بنقصّر الجزء المتغير (عادة اسم) تلقائياً.
     */
    public static function build(callable $template, string $variablePart): string
    {
        $message = $template($variablePart);

        $overflow = mb_strlen($message) - 70;
        if ($overflow > 0) {
            $variablePart = mb_substr($variablePart, 0, max(1, mb_strlen($variablePart) - $overflow));
            $message = $template($variablePart);
        }

        return $message;
    }
}
