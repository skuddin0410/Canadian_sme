<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Email;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;


Route::get('/login', function () {
    return redirect()->route('login');
});

Route::get('/run/command', function () {
    Artisan::call('db:seed', [
      '--class' => 'PermissionSeeder'
    ]);

    echo 'Completed';
});


Route::get('/admin/login', function () {
    return redirect()->route('login');
});

Route::get('/admin', function () {
    return redirect()->route('login');
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

require __DIR__.'/landing.php';

Route::get('/email/open/{id}', function ($id) {
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




Auth::routes();

Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {

   Route::prefix('admin')->group(function () {
       require __DIR__.'/common.php'; //used by all users in web
       require __DIR__.'/admin.php';  //Admin and Admin 
       require __DIR__.'/exhibitor.php';
       require __DIR__.'/helpdesk.php';

       require __DIR__.'/newsletters.php';
       require __DIR__.'/formbuilder.php';
       require __DIR__.'/lead.php';
       require __DIR__.'/badge.php';
   });
   

});

Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {
    require __DIR__.'/ticket.php';
});