# Security / Architecture Improvement Checklist (Priority)

> เอกสารนี้สรุป “งานที่ควรแก้เพื่อความปลอดภัยและความเป็นระเบียบ”
> จากการตรวจสอบโครงสร้างโปรเจกต์ Laravel ในรอบนี้

## P0 (ต้องแก้ทันที / ความเสี่ยงสูง)

### 1) ปิด Authorization bypass ใน BookingPolicy

- **ไฟล์:** `app/Policies/BookingPolicy.php`
- **ปัญหา:** เมธอด `viewAny/view/create/update/delete/export` คืนค่า `true`
  ทั้งหมด ทำให้ role-based access control ไม่ได้บังคับใช้อย่างแท้จริง
- **ผลกระทบที่คาด:**
  ผู้ใช้ที่ไม่ควรเข้าถึง booking/export อาจเข้าถึงข้อมูลผ่าน
  ช่องทางที่ authorization ไม่ถูกบล็อกระดับ policy

- **สิ่งที่ต้องทำ:**
  - กำหนดเงื่อนไข `return $user->hasRole('Admin') ||
    $user->hasRole('Manager')` (หรือ mapping permissions จริง)
  - เฉพาะ `confirm/cancel` คง logic เดิมหรือทำให้สอดคล้องกับ business rule

### 2) ลด dependency จาก middleware อย่างเดียว (เพิ่ม authorize ใน Controller/

ให้สอดคล้องกับ policy)

- **ไฟล์:**
  - `app/Http/Controllers/ContractController.php`
  - `app/Http/Controllers/RoleController.php`
  - `app/Http/Controllers/RoomController.php`
  - `app/Http/Controllers/GuestController.php`
- **ปัญหา:** หลาย controller ไม่มี `$this->authorize(...)` จึงอาศัย middleware
  อย่างเดียว
- **สิ่งที่ต้องทำ:**
  - ใช้ `$this->authorize(...)` หรือ `Gate::authorize(...)` สำหรับ action ที่สำคัญ
    (อย่างน้อย index/show/create/update/destroy/export)
  - ทำให้ policy/authorization เป็น “source of truth”
    ไม่ใช่ “route middleware เป็นด่านเดียว”

## P1 (แก้เร็ว / เสี่ยงระดับกลาง + บำรุงรักษา)

### 3) หยุด/ลดการ log ข้อมูลผู้ใช้ใน Middleware ทุกครั้ง

- **ไฟล์:** `app/Http/Middleware/AdminOnly.php`
- **ปัญหา:** ใช้ `Log::info` ระบุ URI/Method และชื่อผู้ใช้/role ทุก request
- **ผลกระทบที่คาด:**
  privacy/ข้อมูลส่วนบุคคลใน log, log volume สูง, performance เสื่อม
- **สิ่งที่ต้องทำ:**
  - ลดเป็น log เฉพาะ error หรือทำเป็น debug เฉพาะ environment
  - ลบข้อมูลชื่อผู้ใช้/URI รายละเอียด หรือทำให้ truncate

### 4) ตรวจ consistency การ enforce สิทธิ์กับ Export Endpoints

- **ไฟล์ที่เกี่ยว:**
  - `app/Http/Controllers/*Controller.php`
    (export ของ booking/contracts/invoices/rooms/guests/meter/roles)
  - `app/Policies/BookingPolicy.php` (export)
- **ปัญหา:** export มักเป็นช่องทางรั่วข้อมูล
- **สิ่งที่ต้องทำ:**
  - ใส่ authorize/export permission ให้ชัดเจนตาม policy/role

### 5) ปรับ Performance: export ใช้ `chunk()` หรือ cursor (ลด memory)

- **ไฟล์ที่เกี่ยว:**
  - `app/Http/Controllers/ContractController.php` (`export` ใช้ `get()`)
  - `app/Http/Controllers/InvoiceController.php` (`export` ใช้ `chunk`
    แล้ว แต่ตรวจความครบถ้วน)
  - `app/Http/Controllers/RoomController.php` (`export` ใช้ `get()`)
  - `app/Http/Controllers/GuestController.php` (`export` ใช้ `get()`)
  - และอื่น ๆ ที่ใช้ `get()` แล้ววนสร้าง rows จำนวนมาก
- **สิ่งที่ต้องทำ:**
  - ใช้ `chunk(500/1000)` เหมือน BookingController

## P2

### 6) ทำให้ความสัมพันธ์และ Model code “ไม่ปล่อย placeholder/คลุมเครือ”

- **ไฟล์:** `app/Models/Contract.php`
- **ปัญหา:**
  มีเนื้อหาเป็น placeholder (`// ...`,
  `private function convert(...) return $number;`)
  ซึ่งอาจทำให้ business logic ผิดหรือยังไม่เสร็จ
- **สิ่งที่ต้องทำ:**
  - ตรวจ logic จริงของการแปลง/format จำนวนเงิน
    และ remove placeholder

### 7) Uniformize Validation Strategy

- **ไฟล์ตัวอย่าง:** `ContractController` ใช้ `$request->validate(...)`
  โดยตรง ในขณะที่บาง controller ใช้ FormRequest
- **สิ่งที่ต้องทำ:**
  - ใช้ FormRequest ให้สม่ำเสมอ
    (ลด duplication + ทำ test ง่ายขึ้น)

### 8) เพิ่ม Automated tests ให้ cover Authorization

- **ไฟล์ทดสอบที่มีอยู่:**
  `tests/Feature/ModuleAccessTest.php`,
  `tests/Feature/RouteTest.php`, ฯลฯ
- **สิ่งที่ต้องทำ:**
  - เพิ่ม test กรณี “role ไม่ถูกต้อง แต่สามารถเรียก export/CRUD ได้หรือไม่”

## P3 (Refactor/Architecture/ความสะอาดโค้ด)

### 9) แยกซ้ำซ้อนของ export logic ไปเป็น Service/Helper กลาง

- **เป้าหมาย:** ลดการทำ rows/header pattern ซ้ำ ๆ
- **สิ่งที่ต้องทำ:**
  - สร้าง service เช่น `app/Services/ExportService.php`
    หรือใช้ pattern ใกล้เคียง

---

## ขั้นตอนติดตามความคืบหน้า (Checklist)

- [ ] P0-1 BookingPolicy enforce จริง
- [ ] P0-2 เพิ่ม authorize ใน Controller สำคัญ (Contract/Role/Room/Guest)
- [ ] P1-3 ลด Log ใน AdminOnly
- [ ] P1-4 ทำ authorize ให้ชัดกับ export
- [ ] P1-5 ปรับ export ที่ใช้ get() เป็น chunk
- [ ] P2-6 ตรวจ Contract model placeholder
- [ ] P2-7 ทำ validation ให้สม่ำเสมอด้วย FormRequest
- [ ] P2-8 เพิ่ม test ครอบคลุม authorization/export
- [ ] P3-9 refactor export ซ้ำซ้อน
