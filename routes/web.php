<?php
 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\MeterReadingController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Api\TenantsStatusController;
use Illuminate\Support\Facades\Route;
 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
 
Route::middleware(['web'])->group(function () {
 
    // --- Authentication ---
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
 
    // --- Redirect Root to Dashboard ---
    Route::get('/', function () {
        return redirect('/dashboard');
    })->middleware('auth');
 
    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
 
    // --- Protected Routes ---
    Route::middleware('auth')->group(function () {
 
        // 1. Account & Settings
        Route::get('profile', [AccountController::class, 'editProfile'])->name('profile.edit');
        Route::put('profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('settings', [AccountController::class, 'editSettings'])->name('settings.edit');
        Route::put('settings', [AccountController::class, 'updateSettings'])->name('settings.update');
 
        // 2. ห้องพัก (Rooms)
        Route::redirect('/room', '/rooms');
        Route::get('rooms/bulk-create', [RoomController::class, 'bulkCreate'])->name('rooms.bulk-create');
        Route::post('rooms/bulk-store', [RoomController::class, 'bulkStore'])->name('rooms.bulk-store');
        Route::get('rooms/export', [RoomController::class, 'export'])->name('rooms.export');
        Route::resource('rooms', RoomController::class);
 
        // 3. การจอง (Bookings)
        Route::get('bookings/export', [BookingController::class, 'export'])->name('bookings.export');
        Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::resource('bookings', BookingController::class);
 
        // 4. ผู้เข้าพัก (Guests)
        Route::get('guests/export', [GuestController::class, 'export'])->name('guests.export');
        Route::get('guests/bulk-create', [GuestController::class, 'bulkCreate'])->name('guests.bulk-create');
        Route::post('guests/bulk-store', [GuestController::class, 'bulkStore'])->name('guests.bulk-store');
        Route::resource('guests', GuestController::class);
 
        // 5. รายงาน (Reports)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
 
        // 6. รายชื่อผู้เช่า + สถานะห้อง
        Route::get('tenants-status', [TenantsStatusController::class, 'index'])->name('tenants-status.index');
    });
 
    // --- Manager or Admin ---
Route::middleware(['auth'])->group(function () {
        // ── สัญญาเช่า ──
        Route::get('contracts/export', [ContractController::class, 'export'])->name('contracts.export');
        Route::get('contracts/expiring', [ContractController::class, 'expiring'])->name('contracts.expiring');
        Route::get('contracts/{contract}/pdf', [ContractController::class, 'generatePdf'])->name('contracts.pdf');
        Route::resource('contracts', ContractController::class);
 
        // ใบแจ้งหนี้
        Route::get('invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
        Route::post('invoices/{invoice}/paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markAsPaid');
        Route::get('invoices/bulk-create', [InvoiceController::class, 'bulkCreate'])->name('invoices.bulk-create');
        Route::post('invoices/bulk-store', [InvoiceController::class, 'bulkStore'])->name('invoices.bulk-store');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf'); // ✅ เพิ่มบรรทัดนี้
        Route::resource('invoices', InvoiceController::class);
 
        // การซ่อมบำรุง
        Route::get('maintenances/export', [MaintenanceController::class, 'export'])->name('maintenances.export');
        Route::post('maintenances/{maintenance}/start', [MaintenanceController::class, 'startWork'])->name('maintenances.start');
        Route::post('maintenances/{maintenance}/complete', [MaintenanceController::class, 'completeWork'])->name('maintenances.complete');
        Route::resource('maintenances', MaintenanceController::class);
    });
 
    // --- Admin only resources (policy-driven) ---
    Route::middleware(['auth'])->group(function () {
        // --- Facilities ---
        Route::get('facilities/export', [FacilityController::class, 'export'])->name('facilities.export');
        Route::resource('facilities', FacilityController::class);
 
        // --- Meters ---
        Route::get('meters/export', [MeterController::class, 'export'])->name('meters.export');
        Route::get('meters/{meter}/readings/export', [MeterReadingController::class, 'export'])->name('meters.readings.export');
        Route::post('meters/{meter}/readings/monthly', [MeterReadingController::class, 'storeMonthlyAndGenerateInvoice'])->name('meters.readings.monthly');
        Route::resource('meters.readings', MeterReadingController::class)->except(['show']);
        Route::resource('meters', MeterController::class);
        
        // --- Roles ---
        Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export');
        Route::resource('roles', RoleController::class);
    });
 
    Route::fallback(function () {
        abort(404);
    });
});
 