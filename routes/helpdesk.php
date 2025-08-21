<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  
use App\Http\Controllers\AttendeeUserController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\ExhibitorUserController;
use App\Http\Controllers\SupportStaff\HelpdeskUserController;
use App\Http\Controllers\SupportStaff\PasswordResetController;

Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {

	// Route::post('/exhibitor-users/{id}/send-reset', [ExhibitorUserController::class, 'sendResetLink'])
    // ->name('exhibitor-users.send-reset');
    Route::post('/password/send-reset', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.send-reset');

 
});
   Route::controller(PasswordResetController::class)->group(function () {
    Route::get('/password/reset', 'showLinkRequestForm')->name('password.request');
    Route::post('/password/email', 'sendResetLinkEmail')->name('password.email');
    Route::get('/password/reset/{token}', 'showResetForm')->name('password.reset');
    Route::post('/password/reset', 'reset')->name('password.update');
    // Route::patch('/users/{user}/toggle-block', [HelpdeskUserController::class, 'toggleBlock'])
    // ->name('users.toggleBlock');
    Route::patch('/helpdesk/users/{user}/unblock', [HelpdeskUserController::class, 'unblock'])
    ->name('helpdesk.users.unblock');



});

    

Route::group(['middleware' => ['webauth', 'role:Registration Desk']], function () {

	 Route::resource('attendee-users', AttendeeUserController::class);
     Route::resource('staff-profile', StaffProfileController::class);

});