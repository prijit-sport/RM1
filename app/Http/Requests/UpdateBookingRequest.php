<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ควบคุมสิทธิ์ผ่าน Policy ใน Controller แล้ว
    }
 
    public function rules(): array
    {
        return [
            'guest_id'             => ['sometimes', 'required', 'exists:guests,id'],
            'room_id'              => ['sometimes', 'required', 'exists:rooms,id'],
            'check_in_date'        => ['sometimes', 'required', 'date'],
            'check_out_date'       => ['nullable', 'date', 'after:check_in_date'],
            'status'               => ['sometimes', 'required', 'in:pending,confirmed,cancelled'],
            'rent_amount'          => ['nullable', 'numeric', 'min:0'],
            'deposit_amount'       => ['nullable', 'numeric', 'min:0'],
            'electric_meter_start' => ['nullable', 'integer', 'min:0'],
            'water_meter_start'    => ['nullable', 'integer', 'min:0'],
            'notes'                => ['nullable', 'string', 'max:1000'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'guest_id.required'        => 'กรุณาเลือกผู้เช่า',
            'guest_id.exists'          => 'ไม่พบผู้เช่าที่เลือก',
            'room_id.required'         => 'กรุณาเลือกห้องพัก',
            'room_id.exists'           => 'ไม่พบห้องพักที่เลือก',
            'check_in_date.required'   => 'กรุณาระบุวันที่เข้าพัก',
            'check_in_date.date'       => 'รูปแบบวันที่เข้าพักไม่ถูกต้อง',
            'check_out_date.date'      => 'รูปแบบวันที่ออกไม่ถูกต้อง',
            'check_out_date.after'     => 'วันที่ออกต้องหลังจากวันที่เข้าพัก',
            'status.required'          => 'กรุณาเลือกสถานะ',
            'status.in'                => 'สถานะไม่ถูกต้อง',
        ];
    }
}
 