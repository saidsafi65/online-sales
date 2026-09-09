<?php

return [
    // بوابة SMS — أي شركة (HotSMS، 4jawaly، إلخ) بتحتاج نفس الفكرة: مفتاح/كلمة سر
    // ورابط API. القيم بتضل فاضية لحد ما توصل بيانات شركة حقيقية.
    'api_key'      => env('SMS_API_KEY'),
    'api_secret'   => env('SMS_API_SECRET'),
    'sender_id'    => env('SMS_SENDER_ID', 'OnlineSale'), // الاسم اللي بيظهر للمستلم كمرسل
    'base_url'     => env('SMS_BASE_URL'),
];
