# TODO — Production Readiness Audit

## TASK 1: แก้ Race Condition ในการจองห้อง (Critical — Priority สูงสุด)

- [x] 1. แก้ `BookingService::create()` — ย้าย overlap check เข้าใน `DB::transaction()` + lock แถว `rooms` + ใช้ `lockForUpdate()->first()` แทน `exists()`
- [x] 2. แก้ `BookingService::update()` — lock แถว rooms ตามลำดับ id (กัน deadlock) + lock แถว booking + ใช้ `lockForUpdate()->first()` บน overlap query
- [x] 3. สร้าง `tests/Feature/BookingConcurrencyTest.php`
- [x] 4. รัน test (BookingFlowTest + BookingConcurrencyTest + ทั้ง suite) — ผ่านทั้งหมด 110 tests (286 assertions)
- [x] 5. Commit แบบ Conventional Commits

## TASK 3: สร้าง command ตรวจความพร้อม Production + แก้ Pint & คอมเมนต์ Limitation

- [x] 1. สร้าง `app/Console/Commands/CheckProductionReadinessCommand.php` — เช็ค 6 รายการ (APP_ENV, APP_DEBUG, SESSION_SECURE_COOKIE, APP_KEY, APP_URL, LOG_LEVEL) และ render ตาราง ✅/❌ ด้วย `$this->table()`
- [x] 2. สร้าง `tests/Feature/CheckProductionReadinessCommandTest.php` — ครอบคลุมกรณีผ่านและกรณี fail ทุก check (รวม 8 tests)
- [x] 3. อัปเดต `README.md` หัวข้อ "การเตรียมพร้อมสำหรับ Production" — เพิ่มคำแนะนำให้รัน `php artisan app:check-production-readiness` ก่อน deploy ทุกครั้ง
- [x] 4. รัน Pint กับ command + test และยืนยัน `pint --test` ผ่านทั้งโปรเจค (164 files)
- [x] 5. เพิ่มคอมเมนต์ Limitation ของ APP_URL check เหนือ check ข้อ 5 ใน `CheckProductionReadinessCommand.php`
- [x] 6. รัน `php artisan test` ทั้ง suite — ผ่านครบ 118 tests (308 assertions)
- [x] 7. Commit แบบ Conventional Commits (แยก fix สำหรับ restore TODO.md)
