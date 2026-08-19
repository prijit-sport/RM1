# TODO — Production Readiness Audit)
TASK 1.
: แก้ Race Condition ในการจองห้อง (Critical — Priority สูงสุด)

 1. แก้ BookingService::create() — ย้าย overlap check เข้าใน DB::transaction() + lock แถว rooms + ใช้ lockForUpdate()->first() แทน exists()
 2. แก้ BookingService::update() — lock แถว rooms ตามลำดับ id (กัน deadlock) + lock แถว booking + ใช้ lockForUpdate()->first() บน overlap query
 3. สร้าง tests/Feature/BookingConcurrencyTest.php
 4. รัน test (BookingFlowTest + BookingConcurrencyTest + ทั้ง suite) — ผ่านทั้งหมด 110 tests (286 assertions)
 5. Commit แบบ Conventional Commits
TASK 3: สร้าง command ตรวจความพร้อม Production + แก้ Pint & คอมเมนต์ Limitation
 6. สร้าง app/Console/Commands/CheckProductionReadinessCommand.php — เช็ค 6 รายการ (APP_ENV, APP_DEBUG, SESSION_SECURE_COOKIE, APP_KEY, APP_URL, LOG_LEVEL) และ render ตาราง ✅/❌ ด้วย $this->table()
 7. สร้าง tests/Feature/CheckProductionReadinessCommandTest.php — ครอบคลุมกรณีผ่านและกรณี fail ทุก check (รวม 8 tests)
 8. อัปเดต README.md หัวข้อ "การเตรียมพร้อมสำหรับ Production" — เพิ่มคำแนะนำให้รัน php artisan app:check-production-readiness ก่อน deploy ทุกครั้ง
 9. รัน Pint กับ command + test และยืนยัน pint --test ผ่านทั้งโปรเจค (164 files)
 10. เพิ่มคอมเมนต์ Limitation ของ APP_URL check เหนือ check ข้อ 5 ใน CheckProductionReadinessCommand.php
 11. รัน php artisan test ทั้ง suite — ผ่านครบ 118 tests (308 assertions)
 12. Commit แบบ Conventional Commits (แยก fix สำหรับ restore TODO.md)
TASK 4: เพิ่ม Encryption สำหรับข้อมูล PII ของผู้เข้าพัก (High Priority)
 13. สำรวจโครงสร้าง: Guest model, migration ของ guests table, จุด query/filter PII (มีเฉพาะใน GuestController), จุดแสดงผล/export (views + export + NotificationService)
 14. ตรวจสอบข้อมูลจริง: พบว่ามี guest 167 records (ข้อมูลจริง/ทดสอบ) และทั้ง 3 ฟิลด์ (email, phone, id_number) ต้องรองรับ either exact-match (unique) หรือ like-search
 15. สร้าง migration 2026_08_06_134125_add_encrypted_lookup_columns_to_guests_table.php — เพิ่ม email_ciphertext, email_hash (unique), id_number_ciphertext, id_number_hash (unique); เก็บ plaintext column เดิมเป็น safety net
 16. สร้าง app:verify-guest-pii-encryption — ยืนยัน backfill 100% (167/167 email, 167/167 id_number, 0 mismatches)
 17. Cutover — แก้ Guest model (custom accessor/mutator เก็บ ciphertext+hash โดยไม่ใช้ encrypted cast เพื่อรักษา safety net) + GuestController (เปลี่ยน unique validation เป็น hash lookup, เปลี่ยน like-search ของ PII เป็น PHP filter, เพิ่ม piiHash() helper)
 18. รัน full test suite — ผ่านครบ 118 tests (308 assertions) ไม่มี regression
 19. Manual verification ผ่าน tinker: (a) สร้าง guest ใหม่ → email_hash/id_number_hash ถูกสร้าง (ไม่ null), ciphertext ถูกเก็บ, accessor decrypt ได้ถูกต้อง; (b) สร้าง guest ด้วย id_number ซ้ำ → ถูก reject ด้วย UniqueConstraintViolationException; (c) ค้นหาแบบ partial email ("manu") → เจอ guest ที่ถูกต้อง
 20. พบ & แก้ bug: mb_str_contains() ไม่มีใน runtime (undefined) → เปลี่ยนเป็น str_contains() ใน GuestController::index (PHP filter) — แก้ไขแล้ว, test suite ผ่าน 118 tests เหมือนเดิม
TASK 5: แก้ให้ Sanctum API token มีวันหมดอายุ และจำกัด ability ให้เหมาะสม
 21. ตรวจสอบ config/sanctum.php — พบว่า 'expiration' => null ต้องแก้เป็น env value
 22. แก้ config/sanctum.php — เปลี่ยนเป็น env('SANCTUM_TOKEN_EXPIRATION', 1440) (default 24 ชั่วโมง)
 23. เพิ่ม SANCTUM_TOKEN_EXPIRATION=1440 ใน .env.example พร้อมหมายเหตุ
 24. แก้ AuthController::login() — เพิ่มการกำหนด abilities ตามบทบาท (Admin/Staff ได้ ['*'], อื่น ได้ ['dashboard:read']) และคำนวณ $expiresAt จาก config
 25. แก้ bug: Token ถูกสร้างแต่ expiration ไม่ได้ถูก apply — ต้องส่ง $expiresAt เป็น parameter ที่ 3 ให้ createToken()
 26. สร้าง test_login_creates_token_with_expiration() ใน ApiSmokeTest.php — ยืนยัน token->expires_at ไม่ null
 27. รัน full test suite — ผ่านครบ 122 tests (330 assertions)
 28. Commit แบบ Conventional Commits
TASK 6: แก้ปัญหาการโหลดข้อมูลผู้เช่าทั้งหมดเข้าหน่วยความจำก่อน paginate
 29. วิเคราะห์ GuestController::index() — พบว่าใช้ ->get() โหลด 167+ records แล้วอาศัย pagination ใน PHP (memory expensive)
 30. ออกแบบแก้ไข — แยกรูปแบบการค้นหา: email ใช้ exact hash match, ID number ใช้ exact hash match เฉพาะกรณีไม่ใช่ phone-like 0XXXXXXXXX, อื่นๆ ใช้ LIKE search บน name/phone เพื่อให้ SQL paginate ถูกต้องและป้องกัน phone ถูกเข้าใจผิดว่าเป็น id_number
 31. Implement GuestController::index() — ใช้ SQL-level where ตาม looksLikePii(), exact hash lookup สำหรับ PII จริง, LIKE query สำหรับ name/phone, และ ->paginate(10) โดยตรง
 32. เพิ่ม private helper looksLikePii() — แยกกรณีชัดเจน: email (contains '@') เป็น PII; Thai mobile phone (/^0\d{9}$/) ต้อง return false เพื่อให้ LIKE search กับ phone; numeric ID number เป็น preg_match('/^\d{10,13}$/') เท่านั้น
 33. สร้าง 7 test cases ครอบคลุม: partial name ผ่าน, full email ผ่าน, partial email ไม่ผ่าน, full ID ผ่าน, partial ID ไม่ผ่าน, full phone 10 หลักผ่าน, pagination 10/page
 34. รัน GuestControllerTest — ผ่าน 12 tests (35 assertions)
 35. รัน full test suite — ผ่านครบ 129 tests (348 assertions) ไม่มี regression
 36. Commit แบบ Conventional Commits
TASK 7: เตรียมการลบคอลัมน์ plaintext PII (email, id_number) — Phase 1: Audit & Plan
 37. สร้าง Artisan command app:audit-guest-pii-plaintext-usage — สแกนโครงสร้าง runtime เพื่อหาจุดที่อ่าน/เขียนคอลัมน์ plaintext โดยตรง + รายงานเป็นตาราง (ไฟล์, บรรทัด, คอลัมน์, pattern, code snippet)
 38. สร้าง tests/Feature/AuditGuestPiiPlaintextUsageCommandTest.php — ทำให้มั่นใจว่า command ทำงานถูกต้องและแสดงผลลัพธ์อย่างเหมาะสม (5 tests)
 39. สร้าง docs/pii-decommission-plan.md — เอกสารที่อธิบาย (a) เงื่อนไขปลอดภัยสำหรับการลบ plaintext columns (4+ weeks of verify-guest-pii-encryption passes, zero risky plaintext access, database backup, test coverage, rollback plan), (b) ขั้นตอนการลบจริง (migration draft, model update, verification steps), (c) รายการโค้ดที่ต้องแก้ก่อนลบ
 40. รัน php artisan test ทั้ง suite — ผ่านครบ 134 tests (357 assertions) ไม่มี regression (รวม 5 tests ใหม่)
 41. Commit แบบ Conventional Commits

หมายเหตุ (ตรวจสอบย้อนหลัง): เนื้อหาของ TASK 7 ทั้งหมด (audit command, test, และ docs/pii-decommission-plan.md) ถูก commit ขึ้น git ไปแล้วจริงในเชิงเนื้อหา อยู่ใน commit b26aba2 แต่ commit message ที่ใช้ตอนนั้นเป็นข้อความทั่วไป ("update") ไม่ได้อยู่ในรูปแบบ Conventional Commit ตามที่ตั้งใจไว้แต่แรก (feat(pii): prepare for plaintext column decommissioning)

เนื่องจาก commit b26aba2 ถูก push ขึ้น origin/main และมี commit อื่นต่อยอดตามมาแล้ว จึงไม่ทำการ amend/rewrite ประวัติเดิม เพื่อหลีกเลี่ยงความเสี่ยงต่อ commit history ที่แชร์ร่วมกัน บันทึกนี้จึงทำหน้าที่เป็นส่วนเสริมเพื่อให้ตามรอยที่มาของงานได้ถูกต้อง แทนการแก้ไข commit เดิม

TASK 8: เตรียม Cache Layer สำหรับ Redis/Memcached
 42. รวมการสร้าง cache key ทั้งระบบไว้ใน App\Support\CacheKeys โดยคง format key เดิม
 43. เปลี่ยนทุกจุด cache read/invalidation ให้เรียก CacheKeys และเพิ่ม unit test ป้องกัน key format เปลี่ยนโดยไม่ตั้งใจ
 44. เมื่อเปลี่ยน cache store เป็น Redis/Memcached ให้ใช้ Cache::tags() สำหรับ invalidation ตามกลุ่ม
TASK 9: เปลี่ยน is_active login guard ให้ fail-closed
 45. ยืนยัน migration users.is_active เป็น NOT NULL พร้อม default true และ seeder กำหนดค่า active ชัดเจน
 46. เปลี่ยน Web/API authentication จาก fail-open (?? true) เป็น (bool) $user->is_active
 47. ตรวจสอบ AuthLoginTest มี test ป้องกัน user inactive login อยู่แล้ว และรัน full test suite
TASK 10: ตรวจสอบและอัปเดต Dependency Vulnerabilities ก่อน Deploy
 48. รัน composer audit — พบ 41 advisories ใน 12 packages รวม high; อัปเดตภายใน major เดิมด้วย Composer (Laravel 12.53.0 → 12.67.0, Guzzle 7.10.0 → 7.15.3, CommonMark 2.8.0 → 2.10.0 และ Symfony security patches) เพราะ release notes/advisories ระบุว่ามี patched releases ที่ compatible และไม่พบการใช้ API เฉพาะที่เสี่ยง breaking change ในแอป
 49. รัน npm audit — พบ nanoid 3.3.16 (high) และ postcss 8.5.18 (moderate); อัปเดตผ่าน npm audit fix แบบไม่ใช้ --force เป็น nanoid 3.3.18 และ postcss 8.5.26 แล้วเหลือ 0 vulnerabilities
 50. ยืนยันผลหลังอัปเดต: composer audit เหลือ 9 advisories ระดับ medium/low ใน dompdf/dompdf และ symfony/yaml เท่านั้น; ยังไม่อัปเดตตามเกณฑ์ เพราะไม่พบการใช้ Dompdf/YAML โดยตรงหรือการรับ SVG/BMP/YAML จากผู้ใช้ แต่ต้องติดตามและอัปเดตเมื่อมี release ที่ compatible
 51. รัน php artisan test ทั้ง suite — ผ่านครบ 139 tests (369 assertions) ไม่มี regression
TASK 11: แก้ error 500 บนหน้า Dashboard จาก literal @vite() ในคอมเมนต์ (Blade directive collision)
 52. พบสาเหตุ: คอมเมนต์อธิบายใน resources/views/dashboard/index.blade.php เขียน @vite() (วงเล็บว่าง) ไว้ในคอมเมนต์ JavaScript (//) ซึ่ง Blade compiler สแกนหา @word(...) ทั่วทั้งไฟล์แบบดิบโดยไม่สนใจ context ทำให้ compile เป็นการเรียก Vite::__invoke() แบบไม่มี argument จริง เกิด ArgumentCountError (500) ทุกครั้งที่เปิด /dashboard
 53. แก้ไข: escape เป็น @@vite() (เครื่องหมาย @ สองตัว คือวิธี escape ของ Blade) เพื่อไม่ให้ถูกตีความเป็น directive จริง
 54. ยืนยันด้วย Illuminate\View\Compilers\BladeCompiler ตัวจริง — compile ไฟล์แล้ว ไม่มี "Vite" หลงเหลือในผลลัพธ์ และผ่าน php -l (PHP syntax lint) สมบูรณ์
 55. รัน php artisan view:clear เพื่อล้าง compiled cache เก่าที่ค้างจาก error ก่อนหน้า
 56. รัน php artisan test ทั้ง suite — ผ่านครบ 139 tests (369 assertions) ไม่มี regression (รวม DashboardControllerTest และ RouteTest ที่เคย fail จาก bug นี้)
 57. Commit แบบ Conventional Commits — 71d8787
 58. ยืนยันบน production-like environment: เปิด /dashboard จริงผ่านเบราว์เซอร์ กราฟ "สถิติการเช่าห้อง" แสดงผลถูกต้อง ไม่มี error ใน Console, GitHub Actions CI ผ่านครบ
