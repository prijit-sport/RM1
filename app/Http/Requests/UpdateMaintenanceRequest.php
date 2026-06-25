<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['sometimes', 'required', 'exists:rooms,id'],
            'issue_type' => ['sometimes', 'required', 'string', 'max:255'],
            'reported_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'required', 'in:pending,in_progress,completed,cancelled'],

            'description' => ['sometimes', 'nullable', 'string'],
            'assigned_to' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'facility_id' => ['sometimes', 'nullable', 'exists:facilities,id'],
            'maintenance_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'priority' => ['sometimes', 'nullable', 'in:low,medium,high,urgent'],

            'notes' => ['sometimes', 'nullable', 'string'],

            // เผื่อบางฟอร์มส่งมาเพื่อ UI
            'facility_type' => ['sometimes', 'nullable', 'string'],
        ];
    }
}

