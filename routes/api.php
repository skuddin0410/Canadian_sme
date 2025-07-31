<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [App\Http\Controllers\Api\JWTAuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\JWTAuthController::class, 'login']);
Route::post('/login-by-user', [App\Http\Controllers\Api\JWTAuthController::class, 'loginByUser']);
Route::post('/social', [App\Http\Controllers\Api\JWTAuthController::class, 'social']);

Route::prefix('otp')->group(function () {
    Route::post('generate', [App\Http\Controllers\Api\OtpController::class, 'generate']);
    Route::post('verify', [App\Http\Controllers\Api\OtpController::class, 'verify']);
});

Route::prefix('password')->group(function () {
    Route::post('/reset', [App\Http\Controllers\Api\JWTAuthController::class, 'resetPassword']);
});

Route::middleware(['auth:api', 'jwtauth'])->group(function () {
    Route::prefix('me')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\JWTAuthController::class, 'getUser']);
        Route::post('/update', [App\Http\Controllers\Api\JWTAuthController::class, 'updateUser']);
        Route::post('/kyc', [App\Http\Controllers\Api\KycController::class, 'store']);
        Route::post('/bank', [App\Http\Controllers\Api\KycController::class, 'update']);
    });

    Route::prefix('password')->group(function () {
        Route::post('/change', [App\Http\Controllers\Api\JWTAuthController::class, 'changePassword']);
    });

    Route::get('/refresh-token', [App\Http\Controllers\Api\JWTAuthController::class, 'refreshToken']);
    Route::get('/logout', [App\Http\Controllers\Api\JWTAuthController::class, 'logout']);

    Route::get('/referrals', [App\Http\Controllers\Api\JWTAuthController::class, 'referrals']);

    Route::prefix('transactions')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\TransactionController::class, 'index']);
        Route::post('/store', [App\Http\Controllers\Api\TransactionController::class, 'store']);
        Route::post('/show', [App\Http\Controllers\Api\TransactionController::class, 'show']);
        Route::post('/update', [App\Http\Controllers\Api\TransactionController::class, 'update']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::post('/store', [App\Http\Controllers\Api\OrderController::class, 'store']);
        Route::post('/show', [App\Http\Controllers\Api\OrderController::class, 'show']);
        Route::post('/update', [App\Http\Controllers\Api\OrderController::class, 'update']);
    });

    Route::prefix('carts')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\CartController::class, 'index']);
        Route::post('/store', [App\Http\Controllers\Api\CartController::class, 'store']);
    });
    
    //Route::resource('events', EventController::class);
   
});


Route::prefix('blogs')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\BlogController::class, 'index']);
    Route::get('/categories', [App\Http\Controllers\Api\BlogController::class, 'categories']);
    Route::get('/{slug}', [App\Http\Controllers\Api\BlogController::class, 'show']);
});

Route::prefix('banners')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\HomeController::class, 'index']);
});

Route::prefix('testimonials')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\TestimonialController::class, 'index']);
    Route::post('/store', [App\Http\Controllers\Api\TestimonialController::class, 'store']);
});

Route::prefix('faqs')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\FaqController::class, 'index']);
});

Route::prefix('contacts')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\ContactController::class, 'index']);
    Route::post('/store', [App\Http\Controllers\Api\ContactController::class, 'store']);
});
