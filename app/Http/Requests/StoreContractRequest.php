<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
 
    public function rules(): array
    {
        return [
            'contract_number' => ['nullable', 'string', 'max:255', 'unique:contracts,contract_number'],
 
            // ✅ เพิ่ม custom rule ตรวจสอบว่าห้องไม่ได้ occupied อยู่
            'room_id' => [
                'required',
                'exists:rooms,id',
                function ($attribute, $value, $fail) {
                    $room = \App\Models\Room::find($value);
                    if ($room && $room->status === 'occupied') {
                        $fail('ห้องนี้มีผู้เช่าอยู่แล้ว ไม่สามารถสร้างสัญญาได้');
                    }
                },
            ],
 
            'guest_id'              => ['required', 'exists:guests,id'],
            'title'                 => ['required', 'max:200'],
            'contract_date'         => ['nullable', 'date'],
            'landlord_name'         => ['nullable', 'string', 'max:255'],
            'landlord_id_number'    => ['nullable', 'string', 'max:20'],
            'landlord_address'      => ['nullable', 'max:500'],
            'landlord_phone'        => ['nullable', 'string', 'max:20'],
            'start_date'            => ['required', 'date'],
            'end_date'              => ['required', 'date', 'after:start_date'],
            'monthly_rent'          => ['required', 'numeric', 'min:0'],
            'monthly_rent_text'     => ['nullable', 'string', 'max:500'],
            'deposit'               => ['nullable', 'numeric', 'min:0'],
            'advance_payment_months'=> ['nullable', 'integer', 'min:0', 'max:60'],
            'advance_payment'       => ['nullable', 'numeric', 'min:0'],
            'electricity_rate'      => ['nullable', 'numeric', 'min:0'],
            'water_rate'            => ['nullable', 'numeric', 'min:0'],
            'late_fee'              => ['nullable', 'numeric', 'min:0'],
            'other_fees'            => ['nullable', 'max:1000'],
            'status'                => ['required', 'in:draft,pending,active,completed,cancelled'],
            'terms'                 => ['nullable', 'max:5000'],
            'tenant_signature'      => ['nullable', 'string', 'max:255'],
            'landlord_signature'    => ['nullable', 'string', 'max:255'],
            'witness_signature'     => ['nullable', 'string', 'max:255'],
            'tenant_sign_date'      => ['nullable', 'date'],
            'landlord_sign_date'    => ['nullable', 'date'],
            'witness_sign_date'     => ['nullable', 'date'],
            'description'           => ['nullable', 'max:1000'],
            'notes'                 => ['nullable', 'max:1000'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'contract_number.unique'  => 'เลขที่สัญญาซ้ำ โปรดตรวจสอบอีกครั้ง',
            'room_id.required'        => 'กรุณาเลือกห้อง',
            'room_id.exists'          => 'ห้องที่เลือกไม่ถูกต้อง',
            'guest_id.required'       => 'กรุณาเลือกผู้เช่า',
            'guest_id.exists'         => 'ผู้เช่าที่เลือกไม่ถูกต้อง',
            'title.required'          => 'กรุณากรอกหัวข้อ',
            'start_date.required'     => 'กรุณาเลือกวันที่เริ่มสัญญา',
            'end_date.required'       => 'กรุณาเลือกวันสิ้นสุดสัญญา',
            'end_date.after'          => 'วันสิ้นสุดสัญญาต้องมากกว่าวันเริ่มสัญญา',
            'monthly_rent.required'   => 'กรุณากรอกค่าเช่ารายเดือน',
            'monthly_rent.numeric'    => 'ค่าเช่ารายเดือนต้องเป็นตัวเลข',
            'status.required'         => 'กรุณาระบุสถานะสัญญา',
            'status.in'               => 'สถานะสัญญาไม่ถูกต้อง',
        ];
    }
}
 