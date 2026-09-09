<?php

return [
    'merchant_id'  => env('BOP_MERCHANT_ID'),
    'secret_key'   => env('BOP_SECRET_KEY'),
    'base_url'     => env('BOP_BASE_URL', 'https://api.bankofpalestine.example/v1'), // نعدلها لما توصلك الوثائق الفعلية من البنك
    'callback_url' => env('APP_URL') . '/payment/bankofpalestine/callback',
];
