<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  

Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {

	
});