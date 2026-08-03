<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
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
        return [
            'booking_id' => 'required|exists:bookings,id',
            'invoice_number' => [
                'required',
                'max:50',
                Rule::unique('invoices')->ignore($this->input('draft_invoice_id')),
            ],
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
