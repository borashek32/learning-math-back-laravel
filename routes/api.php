<?php

use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\LogoutController;
use App\Http\Controllers\Api\v1\Auth\PasswordRecoveryController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/logout/all', [LogoutController::class, 'logoutFromAllDevices'])->middleware('auth:sanctum');

    Route::group(['prefix' => 'password'], function () {
        Route::post('/recovery', [PasswordRecoveryController::class, 'sendRecoveryCode']);
        Route::post('/reset', [PasswordRecoveryController::class, 'reset']);
    });
});
