<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {

    Route::get('login', 'LoginController@loginPage');

    // ------------------ Route HomePage ------------------
    Route::get('/', 'HomeController@homePage');
    // ------------------ Route HomePage ------------------
});
