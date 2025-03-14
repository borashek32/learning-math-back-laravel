<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [\App\Http\Controllers\Api\v1\Auth\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\v1\Auth\AuthController::class, 'register']);
});
