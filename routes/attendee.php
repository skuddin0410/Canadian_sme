<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  

Route::group(['middleware' => ['webauth', 'role:Attendee']], function () {

	
});