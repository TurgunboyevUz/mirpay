<?php

use Illuminate\Support\Facades\Route;
use TurgunboyevUz\Mirpay\Controllers\MirpayController;

Route::prefix('mirpay')->controller(MirpayController::class)->group(function () {
    Route::get('/success', 'success')->name('mirpay.success');
    Route::get('/fail', 'fail')->name('mirpay.fail');
});