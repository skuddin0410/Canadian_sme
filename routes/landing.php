<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FormBuilderController;

Route::get('/', [LandingController::class, 'index'])->name('front.landing');

Route::get('/page/{slug}', [PageController::class, 'publicPage'])->name('public.page');//Use on Frontend
Route::get('/registration', [FormBuilderController::class, 'showFrontendForm'])->name('registration');
Route::get('app/page/{slug}', [PageController::class, 'appPage'])->name('public.page'); // used on app

// Route::prefix('form-builder')->group(function () {
  Route::post('/forms/{id}/submit', [FormBuilderController::class, 'submitForm'])->name('forms.submit');
// });
Route::get('/schedule', [LandingController::class, 'scheduleIndex'])->name('schedule-index');
Route::get('/profile/{slug}', [LandingController::class, 'profile'])->name('profile');

Route::get('/exhibitors', [LandingController::class, 'exhibitorIndex'])->name('exhibitor-index');
Route::get('/session/{slug}', [LandingController::class, 'session'])->name('session');
Route::get('/exhibitor/{slug}', [LandingController::class, 'exhibitor'])->name('exhibitor');
Route::get('/profile', [LandingController::class, 'profile'])->name('profile');
Route::get('/profile', [LandingController::class, 'attendeeIndex'])->name('profile-index');
Route::get('/sponsor', [LandingController::class, 'sponsorIndex'])->name('sponsor-index');
Route::get('/sponsor/{slug}', [LandingController::class, 'sponsor'])->name('sponsor');
Route::get('/speaker/{slug}', [LandingController::class, 'speaker'])->name('speaker');

Route::get('/venue', [LandingController::class, 'venue'])->name('venue');

Route::get('/update-user/{userId}', [LandingController::class, 'showUpdateForm'])->name('update-user');
Route::put('/update-user/{userId}', [LandingController::class, 'updateUserDetails'])->name('update-user');


Route::prefix('cms')->group(function () {
    Route::get('/{slug}', [PageController::class, 'appContent']);
});
