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

## TASK 4: เพิ่ม Encryption สำหรับข้อมูล PII ของผู้เข้าพัก (High Priority)

- [x] 1. สำรวจโครงสร้าง: `Guest` model, migration ของ `guests` table, จุด query/filter PII (มีเฉพาะใน `GuestController`), จุดแสดงผล/export (views + export + NotificationService)
- [x] 2. ตรวจสอบข้อมูลจริง: พบว่ามี guest 167 records (ข้อมูลจริง/ทดสอบ) และทั้ง 3 ฟิลด์ (email, phone, id_number) ต้องรองรับ either exact-match (unique) หรือ like-search
- [x] 3. สร้าง migration `2026_08_06_134125_add_encrypted_lookup_columns_to_guests_table.php` — เพิ่ม `email_ciphertext`, `email_hash` (unique), `id_number_ciphertext`, `id_number_hash` (unique); เก็บ plaintext column เดิมเป็น safety net
- [x] 4. สร้าง `app:verify-guest-pii-encryption` — ยืนยัน backfill 100% (167/167 email, 167/167 id_number, 0 mismatches)
- [x] 5. Cutover — แก้ `Guest` model (custom accessor/mutator เก็บ ciphertext+hash โดยไม่ใช้ `encrypted` cast เพื่อรักษา safety net) + `GuestController` (เปลี่ยน unique validation เป็น hash lookup, เปลี่ยน like-search ของ PII เป็น PHP filter, เพิ่ม `piiHash()` helper)
- [x] 6. รัน full test suite — ผ่านครบ 118 tests (308 assertions) ไม่มี regression
- [x] 7. Manual verification ผ่าน tinker: (a) สร้าง guest ใหม่ → `email_hash`/`id_number_hash` ถูกสร้าง (ไม่ null), ciphertext ถูกเก็บ, accessor decrypt ได้ถูกต้อง; (b) สร้าง guest ด้วย id_number ซ้ำ → ถูก reject ด้วย `UniqueConstraintViolationException`; (c) ค้นหาแบบ partial email ("manu") → เจอ guest ที่ถูกต้อง
- [x] 8. พบ & แก้ bug: `mb_str_contains()` ไม่มีใน runtime (undefined) → เปลี่ยนเป็น `str_contains()` ใน `GuestController::index` (PHP filter) — แก้ไขแล้ว, test suite ผ่าน 118 tests เหมือนเดิม
