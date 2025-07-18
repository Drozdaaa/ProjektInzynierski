<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RestaurantController;

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

Route::controller(ManagerController::class)->group(function(){
    Route::get('/manager', 'index')->name('users.manager-dashboard');
});

Route::controller(RestaurantController::class)->group(function(){
    Route::get('/restaurants/{id}/edit', 'edit')->name('restaurants.edit');
    Route::put('/restaurants/{id}','update')->name('restaurants.update');
    Route::delete('/restaurants/{restaurant}', 'destroy')->name('restaurants.destroy');
});

Route::controller(EventController::class)->group(function(){
    Route::delete('/manager/{event}', 'destroy')->name('events.destroy');
    Route::patch('/events/{event}/archive', 'archive')->name('events.archive');
    Route::get('/events/{id}/edit', 'edit')->name('events.edit');
    Route::put('/events/{id}','update')->name('events.update');
});

Route::controller(MenuController::class)->group(function(){
     Route::get('/menu/event/{event}', 'show')->name('menus.show');
});
