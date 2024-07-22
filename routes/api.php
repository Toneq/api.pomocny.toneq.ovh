<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StreamController;

Route::group([
    'middleware' => 'api'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'user_profile']);    
});

Route::prefix('stream')->group(function () {
    Route::prefix('event')->group(function () {
        Route::post('send', EventController::class);
    });

    Route::prefix('chat')->group(function () {
        Route::post('message', [StreamController::class, 'sendMessage']);
        Route::delete('message', [StreamController::class, 'deleteMessage']);
        Route::delete('clear', [StreamController::class, 'clearChat']);
    });

    Route::prefix('info')->group(function () {
        Route::post('category', [StreamController::class, 'setCategory']);
        Route::post('title', [StreamController::class, 'setTitle']);
    });

    Route::prefix('user')->group(function () {
        Route::post('permban', [StreamController::class, 'permBan']);
        Route::post('tempban', [StreamController::class, 'tempBan']);
        Route::delete('unban', [StreamController::class, 'unban']);
    });
});

Route::prefix('bots')->group(function () {
    Route::prefix('kick')->group(function () {
        Route::get('otp', [StreamController::class, 'getOTP']);
    });

    // Route::prefix('twitch')->group(function () {
    //     Route::post('otp', EventController::class);
    // });
});
// Route::get('/subscribe/{user}/event', [EventController::class, 'subscribe']);
// Route::get('/search/{search}', [SearchController::class, 'search'])->where('search', '.*');