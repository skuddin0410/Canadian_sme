<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;   
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\AttendeeUserController;
use App\Http\Controllers\ExhibitorUserController;
use App\Http\Controllers\RepresentativeUserController;

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

    Route::get('/role-permission-matrix', [App\Http\Controllers\RoleController::class, 'matrix'])->name('roles.matrix');
    Route::post('/assign-permission', [App\Http\Controllers\RoleController::class, 'assignPermission'])->name('roles.assign.permission');
    Route::get('/roles/create', [App\Http\Controllers\RoleController::class, 'create']);
    Route::post('/roles', [App\Http\Controllers\RoleController::class, 'store']);

    Route::get('users/export/', '\App\Http\Controllers\UserController@export')->name('user_export');
    Route::post('users/import/', '\App\Http\Controllers\UserController@importUser')->name('user_import');
    Route::post('sendmail', '\App\Http\Controllers\UserController@sendTrackedEmail')->name('sendmail_to_user');

    Route::resource('users', App\Http\Controllers\UserController::class);

    Route::resource('exhibitor-users', ExhibitorUserController::class)->parameters([
    'exhibitor-users' => 'exhibitor_user',
]);
    Route::patch('exhibitor-users/{id}/approve', [ExhibitorUserController::class, 'approve'])->name('exhibitor-users.approve');
    Route::get('exhibitor-users/{id}/assign-booth', [ExhibitorUserController::class, 'assignBoothForm'])->name('exhibitor-users.assign-booth-form');
    Route::post('exhibitor-users/{id}/assign-booth', [ExhibitorUserController::class, 'assignBooth'])->name('exhibitor-users.assign-booth');


   


    
     Route::resource('speaker', SpeakerController::class);

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

Route::group(['middleware' => ['webauth', 'role:Admin|Event Admin|Exhibitor Admin']], function () {
 Route::resource('representative-users', RepresentativeUserController::class);
 Route::resource('attendee-users', AttendeeUserController::class);
});