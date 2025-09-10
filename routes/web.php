<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Email;

Route::get('/login', function () {
    return redirect()->route('login');
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
   });
   

});

Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {
    require __DIR__.'/ticket.php';
});