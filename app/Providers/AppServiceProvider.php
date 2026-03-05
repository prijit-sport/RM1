<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Policies\BookingPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;

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
        Gate::policy(Booking::class, BookingPolicy::class);

        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $today = Carbon::today();

            $overdueInvoices = Invoice::query()
                ->whereIn('status', ['sent', 'overdue'])
                ->whereDate('due_date', '<', $today)
                ->count();

            $pendingMaintenance = Maintenance::query()
                ->whereIn('status', ['pending', 'in_progress'])
                ->count();

            $pendingBookings = Booking::query()
                ->where('status', 'pending')
                ->count();

            $expiringContracts = Contract::query()
                ->where('status', 'active')
                ->whereDate('end_date', '>=', $today)
                ->whereDate('end_date', '<=', $today->copy()->addDays(30))
                ->count();

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
                    'url' => route('maintenances.index'),
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
                'notifications' => $notifications,
                'notificationCount' => $notifications->count(),
                'pendingPayments' => $overdueInvoices,
                'pendingMaintenance' => $pendingMaintenance,
            ]);
        });
    }
}

