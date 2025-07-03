<?php

return [
    'merchant_id' => env('MIRPAY_MERCHANT_ID', ''),
    'secret_key'  => env('MIRPAY_SECRET_KEY', ''),

    'routes' => true // routes/mirpay.php dan foydalanish uchun
];
