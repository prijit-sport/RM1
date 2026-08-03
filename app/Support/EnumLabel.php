<?php

namespace App\Support;

class EnumLabel
{
    private const MAP = [
        'room_type' => [
            'fan' => ['en' => 'Fan', 'th' => 'พัด'],
            'air' => ['en' => 'Air Conditioning', 'th' => 'แอร์'],
            // backward compatibility for old saved data
            'air_conditioning' => ['en' => 'Air Conditioning', 'th' => 'แอร์'],
        ],
        'room_status' => [
            'available' => ['en' => 'Available', 'th' => 'ว่าง'],
            'occupied' => ['en' => 'Occupied', 'th' => 'ใช้งาน'],
            'maintenance' => ['en' => 'Maintenance', 'th' => 'ซ่อมบำรุง'],
        ],
        'booking_status' => [
            'pending' => ['en' => 'Pending', 'th' => 'รอยืนยัน'],
            'confirmed' => ['en' => 'Confirmed', 'th' => 'ยืนยันแล้ว'],
            'checked_in' => ['en' => 'Checked In', 'th' => 'เช็คอินแล้ว'],
            'checked_out' => ['en' => 'Checked Out', 'th' => 'เช็คเอาท์แล้ว'],
            'cancelled' => ['en' => 'Cancelled', 'th' => 'ยกเลิก'],
        ],
        'invoice_status' => [
            'pending' => ['en' => 'Pending', 'th' => 'รอชำระ'],
            'paid' => ['en' => 'Paid', 'th' => 'ชำระแล้ว'],
            'overdue' => ['en' => 'Overdue', 'th' => 'เกินกำหนด'],
            'cancelled' => ['en' => 'Cancelled', 'th' => 'ยกเลิก'],
            'draft' => ['en' => 'Draft', 'th' => 'ฉบับร่าง'],
            'sent' => ['en' => 'Sent', 'th' => 'ส่งแล้ว'],
        ],
        'contract_status' => [
            'active' => ['en' => 'Active', 'th' => 'ใช้งาน'],
            'expired' => ['en' => 'Expired', 'th' => 'หมดอายุ'],
            'terminated' => ['en' => 'Terminated', 'th' => 'ยกเลิก'],
            'draft' => ['en' => 'Draft', 'th' => 'ฉบับร่าง'],
            'completed' => ['en' => 'Completed', 'th' => 'เสร็จสิ้น'],
            'cancelled' => ['en' => 'Cancelled', 'th' => 'ยกเลิก'],
        ],
        'item_status' => [
            'active' => ['en' => 'Active', 'th' => 'ใช้งาน'],
            'inactive' => ['en' => 'Inactive', 'th' => 'ไม่ใช้งาน'],
            'out_of_stock' => ['en' => 'Out Of Stock', 'th' => 'สินค้าหมด'],
        ],
        'maintenance_status' => [
            'pending' => ['en' => 'Pending', 'th' => 'รอดำเนินการ'],
            'in_progress' => ['en' => 'In Progress', 'th' => 'กำลังดำเนินการ'],
            'completed' => ['en' => 'Completed', 'th' => 'เสร็จสิ้น'],
            'cancelled' => ['en' => 'Cancelled', 'th' => 'ยกเลิก'],
        ],
    ];

    public static function th(string $group, ?string $value, ?string $fallback = null): string
    {
        if ($value === null || $value === '') {
            return $fallback ?? '';
        }

        $label = self::MAP[$group][$value]['th'] ?? null;

        return $label ?? ($fallback ?? $value);
    }

    public static function bi(string $group, ?string $value, ?string $fallback = null): string
    {
        if ($value === null || $value === '') {
            return $fallback ?? '';
        }

        $entry = self::MAP[$group][$value] ?? null;
        if (! is_array($entry)) {
            return $fallback ?? $value;
        }

        $en = $entry['en'] ?? $value;
        $th = $entry['th'] ?? null;

        if ($th === null || $th === '') {
            return $en;
        }

        return $en.' ('.$th.')';
    }
}
