<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Contract;
use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send invoice reminder email
     */
    public function sendInvoiceReminder(Invoice $invoice): bool
    {
        try {
            $invoice->load(['guest', 'room', 'booking']);
            
            if (!$invoice->guest || !$invoice->guest->email) {
                Log::warning('[Notification] Invoice reminder failed: No guest email', [
                    'invoice_id' => $invoice->id,
                ]);
                return false;
            }

            // In production, send actual email
            // For now, we'll log the notification
            Log::info('[Notification] Invoice reminder sent', [
                'invoice_number' => $invoice->invoice_number,
                'guest_email' => $invoice->guest->email,
                'amount' => $invoice->total,
                'due_date' => $invoice->due_date,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[Notification] Invoice reminder failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send contract expiring notification
     */
    public function sendContractExpiringNotification(Contract $contract): bool
    {
        try {
            $contract->load(['guest', 'room']);
            
            if (!$contract->guest || !$contract->guest->email) {
                Log::warning('[Notification] Contract notification failed: No guest email', [
                    'contract_id' => $contract->id,
                ]);
                return false;
            }

            Log::info('[Notification] Contract expiring notification sent', [
                'contract_number' => $contract->contract_number,
                'guest_email' => $contract->guest->email,
                'end_date' => $contract->end_date,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[Notification] Contract notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send booking confirmation
     */
    public function sendBookingConfirmation(Booking $booking): bool
    {
        try {
            $booking->load(['guest', 'room']);
            
            if (!$booking->guest || !$booking->guest->email) {
                return false;
            }

            Log::info('[Notification] Booking confirmation sent', [
                'booking_id' => $booking->id,
                'guest_email' => $booking->guest->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[Notification] Booking confirmation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk invoice reminders
     */
    public function sendBulkInvoiceReminders(): int
    {
        $overdueInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', now())
            ->with(['guest', 'room'])
            ->get();

        $sentCount = 0;
        /** @var Invoice $invoice */
        foreach ($overdueInvoices as $invoice) {
            if ($this->sendInvoiceReminder($invoice)) {
                $sentCount++;
            }
        }

        Log::info('[Notification] Bulk invoice reminders completed', [
            'total_overdue' => $overdueInvoices->count(),
            'sent' => $sentCount,
        ]);

        return $sentCount;
    }

    /**
     * Send bulk contract expiring notifications
     */
    public function sendContractExpiringReminders(int $days = 30): int
    {
        $expiringContracts = Contract::where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays($days))
            ->with(['guest', 'room'])
            ->get();

        $sentCount = 0;
        /** @var Contract $contract */
        foreach ($expiringContracts as $contract) {
            if ($this->sendContractExpiringNotification($contract)) {
                $sentCount++;
            }
        }

        Log::info('[Notification] Contract expiring reminders completed', [
            'total_expiring' => $expiringContracts->count(),
            'sent' => $sentCount,
        ]);

        return $sentCount;
    }
}
