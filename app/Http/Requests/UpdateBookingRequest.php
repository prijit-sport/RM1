<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Admin') || $this->user()->hasRole('Staff');
    }
 
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $bookingId = $this->route('booking')->id;
 
        return [
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id|unique:bookings,room_id,' . $bookingId,
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after:check_in_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'electric_meter_start' => 'nullable|integer|min:0',
            'water_meter_start' => 'nullable|integer|min:0',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'notes' => 'nullable|string|max:500',
        ];
    }
 
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'guest_id.required' => 'กรุณาเลือกผู้เช่า',
            'guest_id.exists' => 'ผู้เช่าที่เลือกไม่พบในระบบ',
            'room_id.required' => 'กรุณาเลือกห้องพัก',
            'room_id.exists' => 'ห้องพักที่เลือกไม่พบในระบบ',
            'room_id.unique' => 'ห้องนี้ถูกจองแล้ว',
            'check_in_date.required' => 'กรุณาระบุวันที่เข้าพัก',
            'check_in_date.date' => 'วันที่เข้าพักไม่ถูกต้อง',
            'check_out_date.date' => 'วันที่ออกพักไม่ถูกต้อง',
            'check_out_date.after' => 'วันที่ออกพักต้องหลังจากวันที่เข้าพัก',
            'rent_amount.required' => 'ค่าเช่าต้องไม่เว้น',
            'rent_amount.numeric' => 'ค่าเช่าต้องเป็นตัวเลข',
            'rent_amount.min' => 'ค่าเช่าต้องมากกว่า 0',
            'deposit_amount.required' => 'เงินมัดจำต้องไม่เว้น',
            'deposit_amount.numeric' => 'เงินมัดจำต้องเป็นตัวเลข',
            'deposit_amount.min' => 'เงินมัดจำต้องมากกว่า 0',
            'electric_meter_start.integer' => 'เลขมิเตอร์ไฟต้องเป็นตัวเลขเต็ม',
            'water_meter_start.integer' => 'เลขมิเตอร์น้ำต้องเป็นตัวเลขเต็ม',
            'status.required' => 'กรุณาระบุสถานะการจอง',
            'status.in' => 'สถานะการจองไม่ถูกต้อง',
            'notes.max' => 'หมายเหตุต้องไม่เกิน 500 ตัวอักษร',
        ];
    }
}
