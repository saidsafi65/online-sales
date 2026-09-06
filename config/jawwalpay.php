<?php

return [
    'merchant_id'  => env('JAWWALPAY_MERCHANT_ID'),
    'secret_key'   => env('JAWWALPAY_SECRET_KEY'),
    'base_url'     => env('JAWWALPAY_BASE_URL', 'https://api.jawwalpay.example/v1'), // نعدلها لما توصلك الوثائق الفعلية
    'callback_url' => env('APP_URL') . '/payment/jawwalpay/callback',
];