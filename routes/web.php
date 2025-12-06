<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DishController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RoomController;

Route::controller(MainController::class)->group(function () {
    Route::get('/', 'index')->name('main.index');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/auth/login', 'login')->name('login');
    Route::post('/auth/login', 'authenticate')->name('login.authenticate');
    Route::get('/auth/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/auth/register', 'index')->name('register.form');
    Route::post('/auth/register', 'register')->name('register');
});

Route::middleware(['auth', 'can:is-admin'])->controller(AdminController::class)->group(function () {
    Route::get('/admin', 'index')->name('users.admin-dashboard');
});

Route::middleware(['auth', 'can:admin-or-manager'])->controller(ManagerController::class)->group(function () {
    Route::get('/manager', 'index')->name('users.manager-dashboard');
});

Route::middleware(['auth'])->controller(UserController::class)->group(function () {
    Route::get('/user', 'index')->name('users.user-dashboard');
});

Route::controller(RestaurantController::class)->group(function () {
    Route::middleware(['can:admin-or-manager'])->group(function () {
        Route::get('/restaurants/create', 'create')->name('restaurants.create');
        Route::post('/restaurants', 'store')->name('restaurants.store');
        Route::get('/restaurants/{id}/edit', 'edit')->name('restaurants.edit');
        Route::put('/restaurants/{id}', 'update')->name('restaurants.update');
        Route::delete('/restaurants/{restaurant}', 'destroy')->name('restaurants.destroy');
        Route::get('/manager/restaurant', 'index')->name('restaurants.index');
    });
    Route::get('/restaurants/{id}', 'show')->name('restaurants.show');
});

Route::middleware(['auth'])->controller(EventController::class)->group(function () {
    Route::delete('/manager/{event}', 'destroy')->name('events.destroy');
    Route::patch('/events/{event}/status', 'updateStatus')->name('events.update-status');
    Route::get('/events/{id}/edit', 'edit')->name('events.edit');
    Route::put('/events/{id}', 'update')->name('events.update');
    Route::get('/restaurants/{id}/events/create', 'create')->name('events.create');
    Route::post('/restaurants/{id}/events', 'store')->name('events.store');
    Route::get('/restaurants/{restaurant}/events/{event}', 'show')->name('events.show');
    Route::get('/restaurants/{restaurant}/events-calendar', 'calendar')->name('events.calendar');
    Route::get('/events/busy-rooms', 'busyRooms')->name('events.busy-rooms');
});

Route::controller(MenuController::class)->group(function () {
    Route::get('/menu/event/{event}', 'show')->name('menus.show');
    Route::get('/menu', 'index')->name('menus.index');
    Route::get('/restaurants/{restaurant}/menus/create',  'create')->name('menus.create');
    Route::get('/menus/{menu}/edit', 'edit')->name('menus.edit');
    Route::post('/restaurants/{restaurant}/menus', 'store')->name('menus.store');
    Route::put('/menus/{menu}', 'update')->name('menus.update');
    Route::delete('/menus/{menu}', 'destroy')->name('menus.destroy');

    Route::get('/restaurants/{restaurant}/events/{event}/menus/create', 'createForUser')
        ->name('menus.user-create');
    Route::post('/restaurants/{restaurant}/events/{event}/menus', 'storeForUser')
        ->name('menus.user-store');
    Route::get('/events/{event}/menus/edit', 'editForUser')
        ->name('menus.user.edit');
    Route::put('/events/{event}/menus/{menu}', 'updateForUser')
        ->name('menus.user.update');
    Route::post('/restaurants/{restaurant}/events/{event}/menus/amounts', 'updateAmounts')
        ->name('menus.update-amounts');
});

Route::controller(DishController::class)->group(function () {
    Route::get('/restaurants/{restaurant}/dishes', 'index')->name('dishes.index');
    Route::get('/restaurants/{restaurant}/dishes/create', 'create')->name('dishes.create');
    Route::post('/restaurants/{restaurant}/dishes', 'store')->name('dishes.store');
    Route::get('/dishes/{dish}/edit', 'edit')->name('dishes.edit');
    Route::put('/dishes/{dish}', 'update')->name('dishes.update');
    Route::delete('/dishes/{dish}', 'destroy')->name('dishes.destroy');
});

Route::middleware(['auth', 'can:create-custom-menu'])->group(function () {
    Route::get(
        '/restaurants/{restaurant}/menus/create-for-event/{event}',
        [MenuController::class, 'createForEvent']
    )->name('menus.create-for-event');

    Route::post(
        '/restaurants/{restaurant}/menus/store-for-event/{event}',
        [MenuController::class, 'storeForEvent']
    )->name('menus.store-for-event');
});

Route::middleware(['auth'])->controller(RoomController::class)->group(function () {
    Route::get('/restaurants/{restaurant}/rooms/create', 'create')->name('rooms.create');
    Route::post('/restaurants/{restaurant}/rooms', 'store')->name('rooms.store');
    Route::get('/restaurants/{restaurant}/rooms/{room}/edit', 'edit')->name('rooms.edit');
    Route::put('/restaurants/{restaurant}/rooms/{room}', 'update')->name('rooms.update');
    Route::delete('/restaurants/{restaurant}/rooms/{room}', 'destroy')->name('rooms.destroy');
});
