<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminController;

Route::controller(MainController::class)->group(function () {
    Route::get('/', 'index')->name('main.index');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/auth/login', 'login')->name('login');
    Route::post('/auth/login', 'authenticate')->name('login.authenticate');
    Route::get('/auth/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function(){
    Route::get('/auth/register', 'showRegistrationForm')->name('register.form');
    Route::post('/auth/register', 'register')->name('register');
});

Route::controller(AdminController::class)->group(function(){
    Route::get('/admin', 'index')->name('users.admin-dashboard');
});
