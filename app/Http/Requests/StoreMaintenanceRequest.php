<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'exists:rooms,id'],

            // ฟอร์มส่ง maintenance_type (เดิมใช้เป็น issue_type ใน DB)
            'maintenance_type' => ['required', 'string', 'max:255'],

            // ฟอร์มส่ง request_date (เดิมชื่อ reported_date ใน DB)
            'request_date' => ['required', 'date'],

            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],

            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],

            // กรณีมีฟิลด์เพิ่มจากบางหน้าฟอร์ม ให้ปล่อยเป็น nullable เพื่อไม่ชน schema
            'priority' => ['nullable'],
            'facility_id' => ['nullable'],
            'issue_type' => ['nullable'],
            'reported_date' => ['nullable'],
        ];
    }
}

