<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Email;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\EventUserAuthController;

// Route::get('/login', function () {
//     return redirect()->route('login');
// });

Route::get('/run/command', function () {
    Artisan::call('db:seed', [
        '--class' => 'PermissionSeeder'
    ]);

    echo 'Completed';
});


Route::get('/admin/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('admin.login.submit');

Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

Route::get('/generate-user-qrcodes', function () {
    $users = User::all();
    foreach ($users as $user) {
        if (empty($user->qr_code)) {
            qrCode($user->id, 'user');
        }
    }
    return "✅ QR codes generated for all users without one.";
});

Route::get('/users-with-no-name', function () {
    $users = User::whereNull('name')
        ->orWhere('name', '')
        ->orWhereNull('lastname')
        ->orWhere('lastname', '')
        ->whereHas('roles', function ($q) {
            $q->where('name', 'Attendee');
        })
        ->get(['id', 'name', 'lastname', 'email']);


    return  $users;
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return 'All caches cleared!';
});

require __DIR__ . '/landing.php';

Route::get('/email/open/track/{id}', function ($id) {
    $email = Email::find($id);
    if ($email && !$email->opened_at) {
        $email->opened_at = now();
        $email->save();
    }

    // Return a transparent 1x1 pixel
    return response(
        base64_decode(
            'R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='
        )
    )->header('Content-Type', 'image/gif');
});

Route::get('/email/img/{id}', [EmailTrackingController::class, 'trackOpen'])->name('email.track.open');

Route::get('/email/click/{id}', [EmailTrackingController::class, 'trackClick'])->name('email.track.click');

Route::prefix('events/{event}')->group(function () {

    Route::get('/login', [EventUserAuthController::class, 'showLogin'])
        ->name('event.user.login');

    Route::post('/login', [EventUserAuthController::class, 'login'])
        ->name('event.user.login.submit');

    Route::get('/register', [EventUserAuthController::class, 'showRegister'])
        ->name('event.user.register');

});
Auth::routes();

Route::prefix('user')->name('user.')->middleware(['webauth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\UserHomeController::class, 'edit'])->name('home');
    // Route::get('/profile/edit', [App\Http\Controllers\UserHomeController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\UserHomeController::class, 'updateProfile'])->name('profile.update');
    Route::put('/company-details', [App\Http\Controllers\UserHomeController::class, 'updateCompanyDetails'])->name('company.update');
    Route::post('/company-details/file-delete', [App\Http\Controllers\UserHomeController::class, 'deleteCompanyFile'])->name('company.file.delete');

    Route::get('/event/{slug}', [LandingController::class, 'eachEvent'])->name('front.events')->middleware('event.access');
});

Route::get('/login', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return redirect('/admin/home');
        }

        return redirect('/user/home');
    }

    return view('auth.login');
})->name('login');



Route::post('/send-otp', [App\Http\Controllers\EventUserAuthController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [App\Http\Controllers\EventUserAuthController::class, 'verifyOtp'])->name('verify.otp');

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);

Route::group(['middleware' => ['webauth', 'admin', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {

    Route::prefix('admin')->group(function () {
        require __DIR__ . '/common.php'; //used by all users in web
        require __DIR__ . '/admin.php';  //Admin and Admin 
        require __DIR__ . '/exhibitor.php';
        require __DIR__ . '/helpdesk.php';

        require __DIR__ . '/newsletters.php';
        require __DIR__ . '/formbuilder.php';
        require __DIR__ . '/lead.php';
        require __DIR__ . '/badge.php';
    });
});

Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {
    require __DIR__ . '/ticket.php';
});
