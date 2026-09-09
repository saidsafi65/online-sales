<?php

return [
    'merchant_id'  => env('PALPAY_MERCHANT_ID'),
    'secret_key'   => env('PALPAY_SECRET_KEY'),
    'base_url'     => env('PALPAY_BASE_URL', 'https://api.palpay.example/v1'), // نعدلها لما توصلك الوثائق الفعلية من بال باي
    'callback_url' => env('APP_URL') . '/payment/palpay/callback',
];
