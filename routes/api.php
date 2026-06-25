<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

// Protected routes (rate limit to avoid API abuse)
Route::middleware('throttle:60,1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });
});