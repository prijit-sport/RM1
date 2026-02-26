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

// Root redirect to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    // Room routes
    Route::resource('rooms', RoomController::class);
    Route::get('rooms/bulk-create', [RoomController::class, 'bulkCreate'])->name('rooms.bulk-create');
    Route::post('rooms/bulk-store', [RoomController::class, 'bulkStore'])->name('rooms.bulk-store');
    Route::get('rooms/export', [RoomController::class, 'export'])->name('rooms.export');

    // Guest routes
    Route::resource('guests', GuestController::class);
    Route::get('guests/export', [GuestController::class, 'export'])->name('guests.export');

    // Booking routes
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('bookings/export', [BookingController::class, 'export'])->name('bookings.export');

    // Report routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
});

// Employee-level routes (Manager and Admin)
Route::middleware(['auth', 'manager_or_admin'])->group(function () {
    // Item routes
    Route::resource('items', ItemController::class);
    Route::get('items/export', [ItemController::class, 'export'])->name('items.export');

    // Contract routes
    Route::resource('contracts', ContractController::class);
    Route::get('contracts/{id}/pdf', [ContractController::class, 'generatePdf'])->name('contracts.pdf');
    Route::get('contracts/export', [ContractController::class, 'export'])->name('contracts.export');
    Route::get('contracts/expiring', [ContractController::class, 'expiring'])->name('contracts.expiring');

    // Invoice routes
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{id}/paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markAsPaid');
    Route::get('invoices/{id}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::get('invoices/bulk-create', [InvoiceController::class, 'bulkCreate'])->name('invoices.bulk-create');
    Route::post('invoices/bulk-store', [InvoiceController::class, 'bulkStore'])->name('invoices.bulk-store');
    Route::get('invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::post('invoices/remind-all', [InvoiceController::class, 'remindAll'])->name('invoices.remind-all');

    // Maintenance routes
    Route::resource('maintenances', MaintenanceController::class);
    Route::post('maintenances/{id}/start', [MaintenanceController::class, 'startWork'])->name('maintenances.start');
    Route::post('maintenances/{id}/complete', [MaintenanceController::class, 'completeWork'])->name('maintenances.complete');
    Route::get('maintenances/export', [MaintenanceController::class, 'export'])->name('maintenances.export');

    // Facility routes
    Route::resource('facilities', FacilityController::class);
    Route::get('facilities/export', [FacilityController::class, 'export'])->name('facilities.export');

    // Meter routes
    Route::resource('meters', MeterController::class);
    Route::get('meters/export', [MeterController::class, 'export'])->name('meters.export');
    Route::resource('meters.readings', MeterReadingController::class);
    Route::get('meters/{meter}/readings/export', [MeterReadingController::class, 'export'])->name('meters.readings.export');
});

// Admin-only routes
Route::middleware(['auth', 'admin_only'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export');
});
