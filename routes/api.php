<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Rooms
    Route::apiResource('rooms', RoomController::class);
    
    // Guests
    Route::apiResource('guests', GuestController::class);
    
    // Bookings
    Route::apiResource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    
    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid']);
    
    // Contracts
    Route::apiResource('contracts', ContractController::class);
});

