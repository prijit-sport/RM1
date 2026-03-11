<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $invoiceId = $this->route('invoice')?->id ?? null;
        
        return [
            'booking_id' => 'required|exists:bookings,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $invoiceId . '|max:50',
            'amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|max:500',
        ];
    }
}
