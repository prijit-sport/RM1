# TODO — Production Readiness Audit

## TASK 1: แก้ Race Condition ในการจองห้อง (Critical — Priority สูงสุด)

- [x] 1. แก้ `BookingService::create()` — ย้าย overlap check เข้าใน `DB::transaction()` + lock แถว `rooms` + ใช้ `lockForUpdate()->first()` แทน `exists()`
- [x] 2. แก้ `BookingService::update()` — lock แถว rooms ตามลำดับ id (กัน deadlock) + lock แถว booking + ใช้ `lockForUpdate()->first()` บน overlap query
- [x] 3. สร้าง `tests/Feature/BookingConcurrencyTest.php`
- [x] 4. รัน test (BookingFlowTest + BookingConcurrencyTest + ทั้ง suite) — ผ่านทั้งหมด 110 tests (286 assertions)
- [x] 5. Commit แบบ Conventional Commits
