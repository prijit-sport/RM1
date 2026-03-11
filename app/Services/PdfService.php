<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Contract;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfService
{
    /**
     * Generate PDF for Invoice
     */
    public function generateInvoicePdf(Invoice $invoice)
    {
        try {
            $invoice->load(['guest', 'room', 'booking']);
            
            // Check if dompdf is installed
            if ($this->isDomPdfInstalled()) {
                $pdf = Pdf::loadView('invoices.pdf', [
                    'invoice' => $invoice,
                    'guest' => $invoice->guest,
                    'room' => $invoice->room,
                ]);
                
                return $pdf->download('invoice_' . $invoice->invoice_number . '.pdf');
            }
            
            // Fallback: return HTML for printing
            return response()->view('invoices.pdf', [
                'invoice' => $invoice,
                'guest' => $invoice->guest,
                'room' => $invoice->room,
            ]);
        } catch (\Exception $e) {
            Log::error('[PdfService] Invoice PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate PDF for Contract
     */
    public function generateContractPdf(Contract $contract)
    {
        try {
            $contract->load(['guest', 'room']);
            
            if ($this->isDomPdfInstalled()) {
                $pdf = Pdf::loadView('contracts.pdf', [
                    'contract' => $contract,
                    'guest' => $contract->guest,
                    'room' => $contract->room,
                ]);
                
                return $pdf->download('contract_' . $contract->contract_number . '.pdf');
            }
            
            // Fallback: return HTML for printing
            return response()->view('contracts.pdf', [
                'contract' => $contract,
                'guest' => $contract->guest,
                'room' => $contract->room,
            ]);
        } catch (\Exception $e) {
            Log::error('[PdfService] Contract PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if dompdf is installed
     */
    private function isDomPdfInstalled(): bool
    {
        return class_exists(Pdf::class);
    }
}
