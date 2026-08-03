<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContractRequest extends FormRequest
{
    /**
     * Authorize via Policy.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('contracts', 'contract_number')->ignore($this->contract),
            ],
            'room_id' => ['sometimes', 'required', 'exists:rooms,id'],
            'guest_id' => ['sometimes', 'required', 'exists:guests,id'],
            'title' => ['sometimes', 'required', 'max:200'],
            'contract_date' => ['sometimes', 'nullable', 'date'],
            'landlord_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landlord_id_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'landlord_address' => ['sometimes', 'nullable', 'max:500'],
            'landlord_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after:start_date'],
            'monthly_rent' => ['sometimes', 'required', 'numeric', 'min:0'],
            'monthly_rent_text' => ['sometimes', 'nullable', 'string', 'max:500'],
            'deposit' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'advance_payment_months' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:60'],
            'advance_payment' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'electricity_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'water_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'late_fee' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'other_fees' => ['sometimes', 'nullable', 'max:1000'],
            'status' => ['sometimes', 'required', 'in:draft,pending,active,completed,cancelled'],
            'terms' => ['sometimes', 'nullable', 'max:5000'],
            'tenant_signature' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landlord_signature' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witness_signature' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tenant_sign_date' => ['sometimes', 'nullable', 'date'],
            'landlord_sign_date' => ['sometimes', 'nullable', 'date'],
            'witness_sign_date' => ['sometimes', 'nullable', 'date'],
            'description' => ['sometimes', 'nullable', 'max:1000'],
            'notes' => ['sometimes', 'nullable', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'contract_number.unique' => 'เลขที่สัญญาซ้ำ โปรดตรวจสอบอีกครั้ง',
            'room_id.required' => 'กรุณาเลือกห้อง',
            'room_id.exists' => 'ห้องที่เลือกไม่ถูกต้อง',
            'guest_id.required' => 'กรุณาเลือกผู้เช่า',
            'guest_id.exists' => 'ผู้เช่าที่เลือกไม่ถูกต้อง',
            'title.required' => 'กรุณากรอกหัวข้อ',
            'start_date.required' => 'กรุณาเลือกวันที่เริ่มสัญญา',
            'end_date.required' => 'กรุณาเลือกวันสิ้นสุดสัญญา',
            'end_date.after' => 'วันสิ้นสุดสัญญาต้องมากกว่าวันเริ่มสัญญา',
            'monthly_rent.required' => 'กรุณากรอกค่าเช่ารายเดือน',
            'monthly_rent.numeric' => 'ค่าเช่ารายเดือนต้องเป็นตัวเลข',
            'status.required' => 'กรุณาระบุสถานะสัญญา',
            'status.in' => 'สถานะสัญญาไม่ถูกต้อง',
        ];
    }
}
