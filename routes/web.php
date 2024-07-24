<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedisSubscriptionController;
use App\Http\Controllers\TwitchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('provider')->group(function () {
    Route::prefix('twitch')->group(function () {
        Route::get('link', [TwitchController::class, 'getTwitchAuthUrl']);
        Route::get('auth/callback', [TwitchController::class, 'handleTwitchCallback']);
    });
    // Route::prefix('kick')->group(function () {
    //     Route::get('link', [TwitchController::class, 'getTwitchAuthUrl']);
    //     Route::get('auth/callback', [TwitchController::class, 'handleTwitchCallback']);
    // });
});