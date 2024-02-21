<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('order')->group(function () {
    Route::controller('OrderController')->group(function () {
        Route::post('create', 'store');
        Route::get('{id}/status', 'status');
        Route::get('{id}/cancel', 'cancel');
    });
});

Route::prefix('driver')->group(function () {
    Route::controller('DriverController')->group(function () {
        Route::get('{id}/status', 'status');
    });
});
