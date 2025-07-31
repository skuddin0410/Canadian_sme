<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('account-information', [App\Http\Controllers\HomeController::class, 'accountInfo'])->name('change.account.information');
    Route::post('account-information', [App\Http\Controllers\HomeController::class, 'accountInformation'])->name('change.account.information.post');

    Route::get('change-password', [App\Http\Controllers\HomeController::class, 'changeAccountPassword'])->name('admin.change.password');
    Route::post('change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('admin.change.password.post');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::group(['middleware' => ['webauth', 'role:Admin']], function () {
        Route::resource('banners', App\Http\Controllers\BannerController::class);
        Route::resource('pages',   App\Http\Controllers\PageController::class);
        Route::resource('categories', App\Http\Controllers\CategoryController::class);
        Route::resource('cms', App\Http\Controllers\CmsController::class);
        Route::resource('coupons', App\Http\Controllers\CouponController::class);
        Route::resource('drives', App\Http\Controllers\DriveController::class);
        Route::resource('faqs', App\Http\Controllers\FaqController::class);
        Route::resource('otps', App\Http\Controllers\OtpController::class);
        Route::resource('orders', App\Http\Controllers\OrderController::class);
        Route::resource('transactions', App\Http\Controllers\TransactionController::class);
        Route::resource('settings', App\Http\Controllers\SettingController::class);
        Route::resource('testimonials', App\Http\Controllers\TestimonialController::class);
        Route::resource('affiliates', App\Http\Controllers\AffiliateController::class);
        Route::resource('admin-users', App\Http\Controllers\AdminUsersController::class);
        Route::resource('events', App\Http\Controllers\EventController::class);
    
        Route::get('users/export/', '\App\Http\Controllers\UserController@export')->name('user_export');

        Route::resource('users', App\Http\Controllers\UserController::class);

        Route::any('faqs/{id}/order/{order}', '\App\Http\Controllers\FaqController@order');
        Route::any('banners/{id}/order/{order}', '\App\Http\Controllers\BannerController@order');
        Route::any('categories/{id}/order/{order}', '\App\Http\Controllers\CategoryController@order');
        Route::any('testimonials/{id}/order/{order}', '\App\Http\Controllers\TestimonialController@order');
        
        Route::any('home/settings/', '\App\Http\Controllers\SettingController@indexHome')->name('indexHome');
    });

    Route::group(['middleware' => ['webauth', 'role:Affiliate Manager']], function () {
        Route::prefix('affiliate')->group(function () {
            Route::get('/', [App\Http\Controllers\Affiliate\HomeController::class, 'index'])->name('affiliate.index');
            Route::any('/users', [App\Http\Controllers\Affiliate\HomeController::class, 'usersListWithEarning'])->name('affiliate.users');
        });
    });
});
