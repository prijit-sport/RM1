<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Policies\MaintenancePolicy;

use App\Models\Meter;
use App\Models\Role;
use App\Models\Room;
use App\Policies\BookingPolicy;
use App\Policies\ContractPolicy;
use App\Policies\FacilityPolicy;
use App\Policies\GuestPolicy;
use App\Policies\InvoicePolicy; // ✅ แก้ไข: เพิ่ม import InvoicePolicy
use App\Policies\MeterPolicy;
use App\Policies\RolePolicy;
use App\Policies\RoomPolicy;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // กำหนดให้ Pagination ใช้ธีมของ Bootstrap 5
        Paginator::useBootstrapFive();

        // ✅ แก้ไข: รวม Gate::policy ทั้งหมดไว้ที่เดียว ใช้ FQCN สม่ำเสมอ
        Gate::policy(Booking::class,  BookingPolicy::class);
        Gate::policy(Contract::class, ContractPolicy::class);
        Gate::policy(Facility::class, FacilityPolicy::class);
        Gate::policy(Guest::class,    GuestPolicy::class);
        Gate::policy(Invoice::class,  InvoicePolicy::class);  // ✅ แก้ไข: เพิ่ม Invoice policy ที่ขาดไป
        Gate::policy(Meter::class,    MeterPolicy::class);
        Gate::policy(Role::class,     RolePolicy::class);
        Gate::policy(Room::class,     RoomPolicy::class);
        Gate::policy(Maintenance::class, MaintenancePolicy::class);


        // Register @role directive for Blade - improved with null safety
        Blade::directive('role', function ($role) {
            $role = trim($role, "'\"");
            return "<?php if(auth()->check() && auth()->user() && auth()->user()->hasRole('{$role}')): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        // Register @canany directive for multiple permissions
        Blade::directive('canany', function ($permissions) {
            return "<?php if(auth()->check() && auth()->user() && auth()->user()->hasAnyPermission({$permissions})): ?>";
        });

        Blade::directive('endcanany', function () {
            return "<?php endif; ?>";
        });

        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $today = Carbon::today();

            $counts = Cache::remember('layout_notifications', 300, function () use ($today) {
                return [
                    'overdueInvoices' => Invoice::query()
                        ->whereIn('status', ['sent', 'overdue'])
                        ->whereDate('due_date', '<', $today)
                        ->count(),

                    'pendingMaintenance' => Maintenance::query()
                        ->whereIn('status', ['pending', 'in_progress'])
                        ->count(),

                    'pendingBookings' => Booking::query()
                        ->where('status', Booking::STATUS_PENDING)
                        ->count(),

                    'expiringContracts' => Contract::query()
                        ->where('status', 'active')
                        ->whereDate('end_date', '>=', $today)
                        ->whereDate('end_date', '<=', $today->copy()->addDays(config('rm1.contract_expiry_warning_days')))
                        ->count(),
                ];
            });

            $overdueInvoices = $counts['overdueInvoices'];
            $pendingMaintenance = $counts['pendingMaintenance'];
            $pendingBookings = $counts['pendingBookings'];
            $expiringContracts = $counts['expiringContracts'];

            $notifications = collect();

            if ($overdueInvoices > 0) {
                $notifications->push([
                    'message' => __('ui.notifications.overdue_invoices', ['count' => $overdueInvoices]),
                    'url' => route('invoices.index'),
                ]);
            }

            if ($pendingMaintenance > 0) {
                $notifications->push([
                    'message' => __('ui.notifications.pending_maintenance', ['count' => $pendingMaintenance]),
                    'url' => route('maintenances.index', ['status' => 'pending']),
                ]);
            }

            if ($pendingBookings > 0) {
                $notifications->push([
                    'message' => __('ui.notifications.pending_bookings', ['count' => $pendingBookings]),
                    'url' => route('bookings.index', ['status' => 'pending']),
                ]);
            }

            if ($expiringContracts > 0) {
                $notifications->push([
                    'message' => __('ui.notifications.expiring_contracts', ['count' => $expiringContracts]),
                    'url' => route('contracts.expiring'),
                ]);
            }

            $view->with([
                'notifications'      => $notifications,
                'notificationCount'  => $notifications->count(),
                'pendingPayments'    => $overdueInvoices,
                'pendingMaintenance' => $pendingMaintenance,
            ]);
        });
    }
}
