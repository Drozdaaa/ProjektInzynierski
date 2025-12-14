<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DishController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::middleware('auth')->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('users.user-dashboard')->with('success', 'Email został zweryfikowany!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link weryfikacyjny został wysłany ponownie!');
    })->middleware('throttle:6,1')->name('verification.send');
});


Route::middleware('guest')->controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showLinkRequestForm')
        ->name('password.request');

    Route::post('/forgot-password', 'sendResetLinkEmail')
        ->name('password.email');

    Route::get('/reset-password/{token}','showResetForm')
        ->name('password.reset');

    Route::post('/reset-password', 'reset')
        ->name('password.update');
});

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

Route::middleware(['auth', 'can:is-admin', 'prevent-back-history', 'verified'])->controller(AdminController::class)->group(function () {
    Route::get('/admin', 'index')->name('users.admin-dashboard');
});

Route::middleware(['auth', 'can:admin-or-manager', 'prevent-back-history', 'verified'])->controller(ManagerController::class)->group(function () {
    Route::get('/manager', 'index')->name('users.manager-dashboard');
});

Route::middleware(['auth', 'prevent-back-history', 'verified'])->controller(UserController::class)->group(function () {
    Route::get('/user', 'index')->name('users.user-dashboard');
    Route::get('/users/{id}/edit', 'edit')->name('users.edit');
    Route::put('/users/{id}', 'update')->name('users.update');
    Route::put('/users/{id}/password', 'updatePassword')->name('users.update-password');
    Route::delete('/users/{id}', 'destroy')->name('users.destroy');
});

Route::controller(RestaurantController::class)->group(function () {
    Route::middleware(['auth', 'can:admin-or-manager', 'prevent-back-history'])->group(function () {
        Route::get('/restaurants/create', 'create')->name('restaurants.create');
        Route::post('/restaurants', 'store')->name('restaurants.store');
        Route::get('/restaurants/{id}/edit', 'edit')->name('restaurants.edit');
        Route::put('/restaurants/{id}', 'update')->name('restaurants.update');
        Route::delete('/restaurants/{restaurant}', 'destroy')->name('restaurants.destroy');
        Route::get('/manager/restaurant', 'index')->name('restaurants.index');
    });
});

Route::controller(EventController::class)->group(function () {
    Route::get('/restaurants/{id}/events/create', 'create')->name('events.create');
    Route::get('/restaurants/{restaurant}/events-calendar', 'calendar')->name('events.calendar');
    Route::get('/events/busy-rooms', 'busyRooms')->name('events.busy-rooms');
    Route::middleware(['auth', 'prevent-back-history'])->group(function () {
        Route::get('/restaurants/{restaurant}/events/{event}', 'show')->name('events.show');
    });
});

Route::middleware(['auth', 'prevent-back-history'])->controller(EventController::class)->group(function () {
    Route::post('/restaurants/{id}/events', 'store')->name('events.store');
    Route::delete('/manager/{event}', 'destroy')->name('events.destroy');
    Route::patch('/events/{event}/status', 'updateStatus')->name('events.update-status');
    Route::get('/events/{id}/edit', 'edit')->name('events.edit');
    Route::put('/events/{id}', 'update')->name('events.update');
});

Route::controller(MenuController::class)->group(function () {
    Route::middleware(['auth', 'prevent-back-history'])->group(function () {
        Route::get('/menu/event/{event}', 'show')->name('menus.show');
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

    Route::middleware(['auth', 'prevent-back-history', 'can:admin-or-manager'])->group(function () {
        Route::get('/menu', 'index')->name('menus.index');
        Route::get('/restaurants/{restaurant}/menus/create',  'create')->name('menus.create');
        Route::post('/restaurants/{restaurant}/menus', 'store')->name('menus.store');
        Route::get('/menus/{menu}/edit', 'edit')->name('menus.edit');
        Route::put('/menus/{menu}', 'update')->name('menus.update');
        Route::delete('/menus/{menu}', 'destroy')->name('menus.destroy');
    });

    Route::middleware(['auth', 'can:create-custom-menu', 'prevent-back-history'])->group(function () {
        Route::get('/restaurants/{restaurant}/menus/create-for-event/{event}', 'createForEvent')
            ->name('menus.create-for-event');
        Route::post('/restaurants/{restaurant}/menus/store-for-event/{event}', 'storeForEvent')
            ->name('menus.store-for-event');
    });
});

Route::controller(DishController::class)->group(function () {
    Route::middleware(['auth', 'can:admin-or-manager', 'prevent-back-history'])->group(function () {
        Route::get('/restaurants/{restaurant}/dishes', 'index')->name('dishes.index');
        Route::get('/restaurants/{restaurant}/dishes/create', 'create')->name('dishes.create');
        Route::post('/restaurants/{restaurant}/dishes', 'store')->name('dishes.store');
        Route::get('/dishes/{dish}/edit', 'edit')->name('dishes.edit');
        Route::put('/dishes/{dish}', 'update')->name('dishes.update');
        Route::delete('/dishes/{dish}', 'destroy')->name('dishes.destroy');
    });
});

Route::controller(RoomController::class)->group(function () {
    Route::middleware(['auth', 'can:admin-or-manager', 'prevent-back-history'])->group(function () {
        Route::get('/restaurants/{restaurant}/rooms/create', 'create')->name('rooms.create');
        Route::post('/restaurants/{restaurant}/rooms', 'store')->name('rooms.store');
        Route::get('/restaurants/{restaurant}/rooms/{room}/edit', 'edit')->name('rooms.edit');
        Route::put('/restaurants/{restaurant}/rooms/{room}', 'update')->name('rooms.update');
        Route::delete('/restaurants/{restaurant}/rooms/{room}', 'destroy')->name('rooms.destroy');
    });
});
