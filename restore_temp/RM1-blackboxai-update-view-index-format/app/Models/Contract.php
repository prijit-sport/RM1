<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    // ...

    /**
     * ฟังก์ชันแปลงตัวเลข (เช่น แปลงเป็นตัวอักษรภาษาไทย)
     * เพิ่ม 'string' หรือ 'mixed' เข้าไปที่หน้า $number
     */
    private function convert(string $number)
    {
        // โค้ดเดิมของคุณ...
        return $number; 
    }
    
    // หรือถ้าเป็นฟังก์ชันสำหรับอ่านตัวเลขเป็นภาษาไทย (ตัวอย่าง)
    public static function ThaiBaht(string $amount)
    {
        // โค้ดเดิม...
    }
}