<?php

use App\Http\Controllers\Subscription\PaymentController;
use App\Http\Controllers\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subscription Routes
|--------------------------------------------------------------------------
|
| Semua route di sini memerlukan autentikasi dan email verified.
|
*/

Route::middleware(['auth', 'verified'])
    ->prefix('subscription')
    ->name('subscription.')->group(function () {

        // Plans
        Route::get('/plans', [SubscriptionController::class, 'plans'])
            ->name('plans');
        Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])
            ->name('subscribe');

        // Payment
        Route::get('/payment/upload/{subscription}', [PaymentController::class, 'upload'])
            ->name('payment.upload');
        Route::post('/payment/store', [PaymentController::class, 'store'])
            ->name('payment.store');

        // History & Current
        Route::get('/history', [SubscriptionController::class, 'history'])
            ->name('history');
        Route::get('/current', [SubscriptionController::class, 'current'])
            ->name('current');
    });
