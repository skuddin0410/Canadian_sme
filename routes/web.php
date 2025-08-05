<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::group(['middleware' => 'auth:web'], function () {
   require __DIR__.'/common.php'; //used by all users in web
   require __DIR__.'/admin.php';  //Admin and Event Admin 
   require __DIR__.'/exhibitor.php';
   require __DIR__.'/speaker.php';
   require __DIR__.'/attendee.php';
   require __DIR__.'/helpdesk.php';
});
