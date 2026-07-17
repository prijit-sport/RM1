# TODO

- [ ] ตรวจสอบว่ามี migration ที่เพิ่ม guest_id_2 / guest_id_3 อยู่แล้วหรือไม่
- [ ] สร้างไฟล์ migration ใหม่ผ่าน `php artisan make:migration add_guest_id_2_and_3_to_bookings_table --table=bookings` (ผู้ใช้รันเอง ไม่รัน migrate)
- [ ] ระบุชื่อไฟล์ migration ที่ถูกสร้างล่าสุด
- [ ] แก้เนื้อหา migration ให้ตรงตาม template ที่ผู้ให้มา (up/down)
- [ ] ไม่รัน `php artisan migrate` อัตโนมัติ
