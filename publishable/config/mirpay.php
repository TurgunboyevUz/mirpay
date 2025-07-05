<?php

return [
    'merchant_id' => env('MIRPAY_MERCHANT_ID', ''),
    'secret_key'  => env('MIRPAY_SECRET_KEY', ''),

    'transaction_model' => TurgunboyevUz\Mirpay\Models\MirpayTransaction::class,

    'routes' => true // routes/mirpay.php dan foydalanish uchun
];
