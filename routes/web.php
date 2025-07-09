<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(MainController::class)->group(function () {
    Route::get('/main', 'index')->name('main.index');
});
