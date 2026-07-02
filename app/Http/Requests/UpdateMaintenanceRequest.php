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

            // ฟอร์มส่ง issue_type
            'issue_type' => ['sometimes', 'required', 'string', 'max:255'],

            // ฟอร์มส่ง reported_date
            'reported_date' => ['sometimes', 'required', 'date'],

            'status' => ['sometimes', 'required', 'in:pending,in_progress,completed,cancelled'],

            'description' => ['sometimes', 'nullable', 'string'],
            'assigned_to' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'notes' => ['sometimes', 'nullable', 'string'],

            // เผื่อฟิลด์ที่ UI ส่งมา แต่ไม่จำเป็นต้อง validate
            'priority' => ['sometimes', 'nullable'],
            'facility_id' => ['sometimes', 'nullable'],

            // เผื่อฟิลด์ที่ UI ส่งมา แต่ไม่จำเป็นต้อง validate
            'issue_type' => ['sometimes', 'nullable'],
            'reported_date' => ['sometimes', 'nullable'],

            // เผื่อบางฟอร์มส่งมาเพื่อ UI
            'facility_type' => ['sometimes', 'nullable', 'string'],
        ];
    }
}

