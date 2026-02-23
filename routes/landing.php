<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\Frontend\SupportController;
use App\Http\Controllers\Frontend\DemoController;

Route::get('/', [LandingController::class, 'index'])->name('front.landing');
Route::get('/event/{slug}', [LandingController::class, 'eachEvent'])->name('front.events');
Route::get('/all-events', [LandingController::class, 'allEvents'])->name('front.allEvents');
Route::get('/search', [LandingController::class, 'search'])->name('front.landing.search');

//Demo Requests
Route::post('/demo-booking', [DemoController::class, 'store'])->name('demo.submit');


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
Route::get('/venue/app', [LandingController::class, 'getVenuInfoForApp'])->name('venue-app');

Route::get('/update-user/{userId}', [LandingController::class, 'showUpdateForm'])->name('update-user');
Route::put('/update-user/{userId}', [LandingController::class, 'updateUserDetails'])->name('update-user');


Route::prefix('cms')->group(function () {
    Route::get('/{slug}', [PageController::class, 'appContent']);
});

Route::post('/supports/submit', [SupportController::class, 'store'])->name('support.submit');
Route::get('/supports', [SupportController::class, 'index'])->name('support.form');

Route::get('/support', function () {
    return view('frontend.support'); // This will load the support.blade.php view
});

Route::get('/contact-us', function () {
    return view('new_contact_us'); // This will load the contact-us.blade.php view
})->name('contact-us');
Route::post('/contact-submit' , [SupportController::class,'store'])->name('contact-submit');

Route::get('/speakers', [LandingController::class, 'speakerIndex'])->name('speaker-index');

Route::get('/promotional-page', function () {
    return view('frontend.landing'); 
})->name('app-landing');
