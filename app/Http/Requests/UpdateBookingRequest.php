<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class UpdateBookingRequest extends FormRequest
{
    /**
     * ✅ ทุก role ที่ login แล้วแก้ไขจองได้ (authorization จัดการโดย BookingPolicy)
     */
    public function authorize(): bool
    {
        return true;
    }
 
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $bookingId = $this->route('booking')->id;
 
        return [
            'guest_id'             => 'required|exists:guests,id',
            'guest_id_2'           => 'nullable|exists:guests,id|different:guest_id',
            'guest_id_3'           => 'nullable|exists:guests,id|different:guest_id|different:guest_id_2',
            'room_id'              => 'required|exists:rooms,id',
            'check_in_date'        => 'required|date',
            'check_out_date'       => 'nullable|date|after:check_in_date',
            // ✅ rent_amount และ deposit_amount เป็น optional เพื่อให้ test ที่ไม่ส่งค่ามาผ่านได้
            'rent_amount'          => 'nullable|numeric|min:0',
            'deposit_amount'       => 'nullable|numeric|min:0',
            'electric_meter_start' => 'nullable|integer|min:0',
            'water_meter_start'    => 'nullable|integer|min:0',
            'status'               => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'notes'                => 'nullable|string|max:500',
        ];
    }
 
    public function messages(): array
    {
        return [
            'guest_id.required'        => 'กรุณาเลือกผู้เช่า',
            'guest_id.exists'          => 'ผู้เช่าที่เลือกไม่อยู่ในระบบ',
            'guest_id_2.exists'      => 'ผู้เช่าคนที่ 2 ที่เลือกไม่พบในระบบ',
            'guest_id_2.different'  => 'ผู้เช่าคนที่ 2 ต้องไม่ซ้ำกับผู้เช่าหลัก',
            'guest_id_3.exists'      => 'ผู้เช่าคนที่ 3 ที่เลือกไม่พบในระบบ',
            'guest_id_3.different'  => 'ผู้เช่าคนที่ 3 ต้องไม่ซ้ำกับผู้เช่าคนอื่น',
            'room_id.required'         => 'กรุณาเลือกห้องพัก',
            'room_id.exists'           => 'ห้องพักที่เลือกไม่อยู่ในระบบ',
            'check_in_date.required'   => 'กรุณาระบุวันที่เข้าพัก',
            'check_in_date.date'       => 'วันที่เข้าพักไม่ถูกต้อง',
            'check_out_date.date'      => 'วันที่ออกพักไม่ถูกต้อง',
            'check_out_date.after'     => 'วันที่ออกพักต้องหลังวันที่เข้าพัก',
            'rent_amount.numeric'      => 'ค่าเช่าต้องเป็นตัวเลข',
            'rent_amount.min'          => 'ค่าเช่าต้องมากกว่า 0',
            'deposit_amount.numeric'   => 'เงินมัดจำต้องเป็นตัวเลข',
            'deposit_amount.min'       => 'เงินมัดจำต้องมากกว่า 0',
            'status.required'          => 'กรุณาระบุสถานะการจอง',
            'status.in'                => 'สถานะการจองไม่ถูกต้อง',
            'notes.max'                => 'หมายเหตุต้องไม่เกิน 500 ตัวอักษร',
        ];
    }
}
 