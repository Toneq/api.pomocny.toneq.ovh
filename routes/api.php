<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserEventSubscriptionController;
use App\Http\Controllers\SearchController;

Route::group([
    'middleware' => 'api'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'user_profile']);    
});
Route::post('/test-notification', [Controller::class, 'test_notification']);
Route::post('/test-wink', [Controller::class, 'test_wink']);
Route::post('/test-message', [Controller::class, 'test_message']);
Route::get('/subscribe/{prefix}', [UserEventSubscriptionController::class, 'subscribe']);
Route::get('/search/{search}', [SearchController::class, 'search'])->where('search', '.*');