# RM1 — สรุปสถาปัตยกรรมระบบ (Architecture Overview)

เอกสารนี้สรุปโครงสร้างทางเทคนิคของระบบ RM1 สำหรับนักพัฒนาที่เข้ามาดูแลต่อ หรือใช้ประกอบการนำเสนอ

---

## 1. Tech Stack

| ส่วน | เทคโนโลยี | เวอร์ชัน |
|---|---|---|
| Backend Framework | Laravel | 12.x |
| ภาษา | PHP | ^8.2 |
| ฐานข้อมูล | MySQL (production) / SQLite in-memory (testing) | - |
| Frontend | Blade Templates + Bootstrap 5 + Vite | Vite 7 |
| Authentication | Session-based (Web) + Sanctum (API) | Sanctum ^4.0 |
| PDF (ถ้าจำเป็นในอนาคต) | barryvdh/laravel-dompdf | ^3.1 (ติดตั้งไว้ แต่ไม่ได้ใช้งานฟีเจอร์ PDF ในปัจจุบัน) |
| Testing | PHPUnit | ^11.5 |

---

## 2. รูปแบบสถาปัตยกรรม

ระบบใช้ **MVC + Service Layer + Policy-based Authorization**:

```
Route → Middleware (auth / role check) → Controller → FormRequest (validation)
      → Service (business logic) → Model (Eloquent) → Blade View
```

### การแบ่งชั้นความรับผิดชอบ

- **Controllers** (`app/Http/Controllers/`) — รับ request, เรียก Service/Model, ส่งข้อมูลไปหน้า View ไม่ใส่ business logic หนักไว้ในนี้
- **Form Requests** (`app/Http/Requests/`) — ตรวจสอบความถูกต้องของข้อมูล (validation) แยกออกจาก Controller
- **Services** (`app/Services/`) — เก็บ business logic ที่ซับซ้อน มี 6 ตัว:
  - `BookingService` — จัดการการจอง/เช็คอิน/เช็คเอาท์
  - `ContractService` — จัดการสัญญาเช่า
  - `InvoiceService` — คำนวณและจัดการใบแจ้งหนี้
  - `MeterBillingService` — คำนวณค่าน้ำ/ไฟจากมิเตอร์
  - `NotificationService` — ส่งการแจ้งเตือน (ใบแจ้งหนี้ค้างชำระ, สัญญาใกล้หมดอายุ)
  - `ReportService` — รวบรวมข้อมูลสำหรับหน้ารายงาน/แดชบอร์ด
- **Policies** (`app/Policies/`) — กำหนดสิทธิ์การเข้าถึงแต่ละ resource (9 Policy ครอบคลุมทุกโมดูลหลัก)
- **Models** (`app/Models/`) — Eloquent Model แทนตารางฐานข้อมูล พร้อม relation และ scope

---

## 3. ระบบสิทธิ์ผู้ใช้งาน (Authorization)

ระบบมี Role 4 ระดับ (`app/Models/Role.php`):

| Role | Constant | คำอธิบาย |
|---|---|---|
| Admin | `Role::ADMIN` | ผู้ดูแลระบบสูงสุด เข้าถึงทุกส่วน |
| Manager | `Role::MANAGER` | ผู้จัดการ จัดการทรัพยากรส่วนใหญ่ได้ |
| Staff | `Role::STAFF` | พนักงาน ทำงานประจำวัน (จอง, มิเตอร์, ซ่อมบำรุง) |
| User | `Role::USER` | ผู้ใช้งานทั่วไป สิทธิ์จำกัด |

Middleware ที่ใช้ตรวจสิทธิ์ (`routes/web.php`): `auth`, `manager_or_admin`, `admin_only`
Policy ตรวจสอบสิทธิ์แบบละเอียดต่อ resource ผ่าน `$this->authorize()` ในทุก Controller

---

## 4. โมดูลหลักของระบบ

| โมดูล | Controller | Model | ความสัมพันธ์หลัก |
|---|---|---|---|
| ห้องพัก | RoomController | Room | 1 ห้อง → หลาย Booking, Facility, Meter, Maintenance |
| ผู้เช่า | GuestController | Guest | 1 ผู้เช่า → หลาย Booking, Contract |
| การจอง | BookingController | Booking | ผูกกับ Room + Guest (+ guest_id_2, guest_id_3 สำหรับผู้เช่าร่วม) → มี Invoice |
| สัญญาเช่า | ContractController | Contract | ผูกกับ Room + Guest (ไม่เชื่อมกับ Invoice โดยตรง — ออกใบแจ้งหนี้ด้วยมือ) |
| ใบแจ้งหนี้ | InvoiceController | Invoice | ผูกกับ Booking, มี `invoice_type` แยก rent/utility |
| มิเตอร์น้ำ-ไฟ | MeterController, MeterReadingController | Meter, MeterReading | ผูกกับ Room, คำนวณบิลอัตโนมัติผ่าน MeterBillingService |
| ซ่อมบำรุง | MaintenanceController | Maintenance | ผูกกับ Room และ Facility (nullable) |
| สิ่งอำนวยความสะดวก | FacilityController | Facility | ผูกกับ Room (nullable — ลบห้องไม่ลบ Facility) |
| บทบาทและสิทธิ์ | RoleController | Role, Permission | ใช้ทั่วทั้งระบบผ่าน Policy |
| รายงาน | ReportController, DashboardController | (ผ่าน ReportService) | รวบรวมข้อมูลจากทุกโมดูล |

---

## 5. Flow สำคัญที่ควรเข้าใจ

### Meter → Invoice (อัตโนมัติ)

```
บันทึกมิเตอร์ (HTTP POST) → MeterBillingService::recordMonthlyAndCreateInvoice()
  → สร้าง draft Invoice (invoice_type = utility) → redirect ไปหน้ายืนยัน
  → พนักงานกดยืนยัน → Invoice เปลี่ยนสถานะเป็น sent
```

### Contract → Invoice (ด้วยมือ — ตัดสินใจแล้วว่าไม่ทำอัตโนมัติ)

```
สร้างสัญญา (Contract) → พนักงานสร้างใบแจ้งหนี้ค่าเช่าเองทุกเดือนผ่านหน้า Invoice
(ไม่มีการเชื่อมต่ออัตโนมัติระหว่างสองโมดูลนี้โดยตั้งใจ)
```

### Booking → Invoice (สร้างด้วยมือหรือ bulk-create)

```
BookingController → InvoiceController::bulkCreateFromBookings() หรือสร้างทีละใบ
```

---

## 6. การจัดการ Cache

ระบบใช้ `CACHE_STORE=file` (ไม่รองรับ Cache Tags) จุดที่ใช้ cache:

- `FacilityController::index()` — cache key ผันแปรตาม filter/page (`facilities.list.{hash}`) TTL 5 นาที
- `RoleController` — cache รายการ permissions (`permissions.all`) TTL 6 ชั่วโมง

**ข้อควรระวัง**: ถ้าเปลี่ยนไปใช้ Redis/Memcached ในอนาคต ควรพิจารณาใช้ Cache Tags แทนการคำนวณ key เอง เพื่อให้ invalidate cache ได้แม่นยำกว่า

---

## 7. Test Suite

รวม 100 test methods (ณ วันที่ปรับปรุงเอกสารนี้ล่าสุด) แบ่งเป็น:

- **Feature Test** (ส่วนใหญ่) — ทดสอบผ่าน HTTP request จริง (`$this->post()`, `$this->get()`)
- **Unit Test** (`tests/Unit/`) — ทดสอบ Service class โดยตรง ไม่ผ่าน HTTP (เน้นการคำนวณเงิน: `InvoiceServiceCalculationTest`, `MeterBillingServiceComputeTest`)
- **End-to-End Flow Test** — ทดสอบ flow เต็มรูปแบบข้ามหลาย Controller (`MeterToInvoiceEndToEndTest`, `BookingFlowTest`, `ContractFlowTest`)

รันทดสอบทั้งหมด: `php artisan test`

---

## 8. ประวัติการตรวจสอบคุณภาพ

โปรเจกต์ผ่านการตรวจสอบคุณภาพอย่างละเอียด 6 รอบ พบและแก้ไขปัญหารวม 18 รายการ ครอบคลุม: route mismatch, authorization hardcode, mass-assignment, timing attack, schema drift, duplicate validation key, misleading business metric, missing seeder data, และ cache key collision — รายละเอียดแต่ละรอบดูได้จากรายงานตรวจสอบที่เก็บไว้แยกต่างหาก
