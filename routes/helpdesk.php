<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  

Route::group(['middleware' => ['webauth', 'role:Support Staff Or Helpdesk']], function () {

	
});

Route::group(['middleware' => ['webauth', 'role:Registration Desk']], function () {

	
});