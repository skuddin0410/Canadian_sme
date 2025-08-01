<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;   

Route::group(['middleware' => ['webauth', 'role:Admin|Event Admin']], function () {
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

    Route::prefix('audit')->group(function () {
        Route::get('/', [App\Http\Controllers\AuditController::class, 'index'])->name('audit.index');
        Route::get('/{log}', [App\Http\Controllers\AuditController::class, 'show'])->name('audit.show');
        Route::get('/entity/{entityType}/{entityId}', [App\Http\Controllers\AuditController::class, 'entityLogs'])->name('audit.edit');
    });
});