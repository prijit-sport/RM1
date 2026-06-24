# TODO - BlackboxAI implementation

## Phase 1: Booking / BookingService hardening

- [ ] ตรวจ/ปรับ Booking model (state transition helpers + consistency)
- [ ] ปรับ BookingService ให้ transaction ครอบคลุม side-effects ทุกจุด
- [ ] เพิ่ม row-lock หรือป้องกัน race condition ตอน lock/release ห้อง

## Phase 2: Authorization consistency

- [ ] ตรวจ policy vs middleware usage ให้ไม่ซ้ำซ้อน
- [ ] ปรับ BookingPolicy ให้สอดคล้องกับ state transition (ถ้าจำเป็น)
- [ ] ปรับ MaintenancePolicy ให้ action เฉพาะ (startWork/completeWork/byRoom) ถ้าจำเป็น

## Phase 3: Exception handler + error pages

- [ ] ปรับ bootstrap/app.php exception render ให้ถูกประเภทและไม่คืน null
- [ ] ทวน error views: 403/404/500

## Phase 4: DB Transaction correctness

- [ ] ทบทวนทุก public method ของ BookingService และ side effects

## Phase 5: Seeder security

- [ ] ปรับ DatabaseSeeder: ลด/หยุดการพิมพ์ password ลง console
- [ ] ทำให้ password เป็นไปตาม requirement ที่ปลอดภัย

## Phase 6: Migrations maintenance_type

- [ ] หา migration ที่เกี่ยวกับ `maintenance_type`
- [ ] ปรับ migration ให้ idempotent + down() ไม่พัง

## Phase 7: Tests / Verification

- [ ] รัน php artisan test (ถ้ามี)
- [ ] รัน php artisan migrate:fresh หรือ migrate ตามสถานการณ์
