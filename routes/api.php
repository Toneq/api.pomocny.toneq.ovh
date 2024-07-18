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

Route::post('/test-message', [EventController::class, 'test_message']);
Route::post('/test-follow', [EventController::class, 'test_follow']);
Route::post('/message-test', [StreamController::class, 'sendMessage']);
Route::post('/ban-test', [StreamController::class, 'permBan']);
Route::post('/dm-test', [StreamController::class, 'deleteMessage']);
Route::post('/ub-test', [StreamController::class, 'unban']);
Route::post('/kat', [StreamController::class, 'createKickToken']);
// Route::get('/subscribe/{user}/event', [EventController::class, 'subscribe']);
// Route::get('/search/{search}', [SearchController::class, 'search'])->where('search', '.*');