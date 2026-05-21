<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ควบคุมสิทธิ์ผ่าน Policy ใน Controller แล้ว
    }

    public function rules(): array
    {
        return [
            'guest_id'             => ['required', 'exists:guests,id'],
            'room_id'              => ['required', 'exists:rooms,id'],
            'check_in_date'        => ['required', 'date', 'after_or_equal:today'],
            'status'               => ['required', 'in:pending,confirmed,cancelled'],
            'rent_amount'          => ['nullable', 'numeric', 'min:0'],
            'deposit_amount'       => ['nullable', 'numeric', 'min:0'],
            'electric_meter_start' => ['nullable', 'integer', 'min:0'],
            'water_meter_start'    => ['nullable', 'integer', 'min:0'],
            'notes'                => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $roomId   = $this->input('room_id');
            $checkIn  = $this->input('check_in_date');
            $checkOut = $this->input('check_out_date');

            if (! $roomId || ! $checkIn || ! $checkOut) {
                return;
            }

            $overlap = \App\Models\Booking::where('room_id', $roomId)
                ->whereIn('status', ['pending', 'confirmed', 'cancelled'])
                ->where(function ($q) use ($checkIn, $checkOut) {
                    // overlap when: existing.check_in < new.check_out AND existing.check_out > new.check_in
                    // boundary case checkout == other checkin should NOT overlap
                    $q->where('check_in_date', '<', $checkOut)
                      ->where('check_out_date', '>', $checkIn);
                })
                ->exists();

            if ($overlap) {
                $validator->errors()->add('room_id', 'Overlapping booking');
            }
        });
    }
 
    public function messages(): array
    {
        return [
            'guest_id.required'      => 'กรุณาเลือกผู้เช่า',
            'guest_id.exists'        => 'ไม่พบผู้เช่าที่เลือก',
            'room_id.required'       => 'กรุณาเลือกห้องพัก',
            'room_id.exists'         => 'ไม่พบห้องพักที่เลือก',
            'check_in_date.required' => 'กรุณาระบุวันที่เข้าพัก',
            'check_in_date.date'     => 'รูปแบบวันที่ไม่ถูกต้อง',
            'check_in_date.after_or_equal' => 'วันที่เข้าพักต้องเป็นวันนี้หรือหลังจากนี้',
            'status.required'        => 'กรุณาเลือกสถานะ',
            'status.in'              => 'สถานะไม่ถูกต้อง',
        ];
    }
}
