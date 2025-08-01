<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  

Route::group(['middleware' => ['webauth', 'role:Exhibitor Admin']], function () {

	
});

Route::group(['middleware' => ['webauth', 'role:Exhibitor Representative']], function () {

	
});