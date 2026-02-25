<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\MeterReadingController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    // Room routes (for all authenticated users)
    Route::resource('rooms', RoomController::class);

    // Guest routes (for all authenticated users)
    Route::resource('guests', GuestController::class);

    // Booking routes (for all authenticated users)
    Route::resource('bookings', BookingController::class);

    // Report routes (for all authenticated users)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Employee-level routes (Employee and Admin)
Route::middleware(['auth', 'manager_or_admin'])->group(function () {
    Route::resource('items', ItemController::class);
    Route::resource('contracts', ContractController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::resource('maintenances', MaintenanceController::class);
    Route::resource('facilities', FacilityController::class);
});

// Admin-only routes
Route::middleware(['auth', 'admin_only'])->group(function () {
    Route::resource('roles', RoleController::class);
});

// Meter routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::resource('meters', MeterController::class);
    Route::resource('meters.readings', MeterReadingController::class);
});
