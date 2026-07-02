# API Documentation

> เอกสารฉบับนี้สรุป API ที่กำหนดไว้ใน `routes/api.php` และพฤติกรรมจาก Controller ภายใต้ `app/Http/Controllers/Api/` เป็นหลัก

---

## Base URL

- โดยปกติ: `http://localhost:8000/api`
- (หากโปรเจกต์ถูกตั้งค่าผ่านโดเมน/พอร์ตอื่น ให้เปลี่ยนตามสภาพแวดล้อมจริง)

---

## Authentication (Bearer token / Sanctum)

- ใช้ **Laravel Sanctum**
- ส่ง Token ผ่าน header:
  - `Authorization: Bearer <access_token>`
- Routes ที่อยู่ภายใต้ `middleware('auth:sanctum')` ต้องมี token ที่ถูกต้อง

> หมายเหตุ: Token จะได้จาก endpoint `POST /api/login`

---

## Endpoints

### 1) Login

- **Method + Path:** `POST /api/login`
- **Description (ภาษาไทย):** เข้าสู่ระบบด้วย `email` และ `password` แล้วออก **access token** สำหรับเรียกใช้ API แบบยืนยันตัวตน
- **Request parameters/body:**
  - `email` (string, required, ต้องเป็นรูปแบบอีเมล)
  - `password` (string, required)

ตัวอย่าง body:

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

- **Response example (JSON):** (กรณีสำเร็จ)

```json
{
  "data": {
    "user": {
      "id": 1,
      "email": "user@example.com",
      "role": {
        "id": 2,
        "name": "admin"
      }
    },
    "token": "plain_text_access_token"
  }
}
```

- **HTTP status codes ที่เป็นไปได้:**
  - `200` (สำเร็จ)
  - `401` (credentials ไม่ถูกต้อง / บัญชีไม่ active)
  - `422` (validation error)

---

### 2) Logout

- **Method + Path:** `POST /api/logout`
- **Description (ภาษาไทย):** ยกเลิก token ปัจจุบัน (logout)
- **Authentication:** ต้องใช้ `Bearer token`
- **Request parameters/body:** ไม่มี (ใช้ `request->user()`)

- **Response example (JSON):**

```json
{
  "message": "Logged out successfully"
}
```

- **HTTP status codes ที่เป็นไปได้:**
  - `200` (สำเร็จ)
  - `401` (ไม่มี/token ไม่ถูกต้อง)

---

### 3) Me (Profile)

- **Method + Path:** `GET /api/me`
- **Description (ภาษาไทย):** ดึงข้อมูลผู้ใช้ที่กำลังใช้งานอยู่ (พร้อม role)
- **Authentication:** ต้องใช้ `Bearer token`
- **Request parameters/body:** ไม่มี

- **Response example (JSON):**

```json
{
  "data": {
    "id": 1,
    "email": "user@example.com",
    "role": {
      "id": 2,
      "name": "admin"
    }
  }
}
```

- **HTTP status codes ที่เป็นไปได้:**
  - `200`
  - `401` (ไม่มี/token ไม่ถูกต้อง)

---

### 4) Dashboard

- **Method + Path:** `GET /api/dashboard`
- **Description (ภาษาไทย):** สรุปข้อมูล Dashboard แบบ KPI/รายได้/สถิติห้อง/รายการล่าสุด/บิลค้างชำระ
- **Authentication:** ต้องใช้ `Bearer token`
- **Request parameters/body:** ไม่มี

- **Response example (JSON):**

```json
{
  "room_count": 10,
  "occupied_count": 3,
  "available_count": 6,
  "maintenance_count": 1,
  "guest_count": 25,
  "booking_count": 120,
  "expiring_contracts": 2,
  "current_month_revenue": 50000,
  "revenue_percent_change": 5.25,
  "monthly_bookings": [
    { "label": "ม.ค. 2566", "count": 10 },
    { "label": "ก.พ. 2566", "count": 12 }
  ],
  "room_type_stats": {
    "fan": { "available": 5, "occupied": 2, "maintenance": 0 },
    "air": { "available": 1, "occupied": 1, "maintenance": 0 }
  },
  "recent_bookings": [
    {
      "id": 101,
      "room_number": "A-101",
      "room_id": 1,
      "guest_name": "สมชาย ใจดี",
      "check_in_date": "01/07/2026",
      "status": "confirmed"
    }
  ],
  "pending_invoices": [
    {
      "id": 301,
      "invoice_number": "INV-2026-0001",
      "guest_name": "สมชาย ใจดี",
      "total": 2500,
      "due_date": "10/07/2026",
      "is_overdue": false
    }
  ]
}
```

- **HTTP status codes ที่เป็นไปได้:**
  - `200`
  - `401` (ไม่มี/token ไม่ถูกต้อง)

---

### 5) Tenants Status (รวมข้อมูลผู้เช่า/สถานะห้อง/สถานะบิล)

- **Method + Path:** `GET /api/tenants-status`
- **Description (ภาษาไทย):** รวมรายการผู้เช่าที่พักอยู่ พร้อมตัวกรองตามโซน และคำค้นหา (ค้นจากชื่อผู้เช่า/เลขห้อง)

- **Authentication:** *(จาก code base Controller/route เดิมอาจไม่ถูกระบุในไฟล์นี้)* ให้เรียกใช้ตามการกำหนด middleware ของ `routes/api.php`.
  - หมายเหตุ: เอกสารนี้อ้างอิงเฉพาะ Controller ที่มีอยู่ และ route path จะตรวจสอบจาก `routes/api.php` เป็นหลัก

- **Request parameters (Query string):**
  - `zone` (string, optional) — ฟิลเตอร์โซนห้อง
  - `search` (string, optional) — คำค้นหา (ค้นจาก `first_name`, `last_name`, และ `rooms.room_number`)
  - (ระบบใช้ pagination: `paginate(15)`)

ตัวอย่าง query:

- `/api/tenants-status?zone=AC-1&search=สมชาย`

- **Response example (JSON):**
  - Endpoint นี้ใน Controller คืนค่า `view('tenants-status.index', ...)` ดังนั้นคำตอบอาจเป็น **HTML** ไม่ใช่ JSON (ขึ้นกับการเรียกจากฝั่ง client)

- **HTTP status codes ที่เป็นไปได้:**
  - `200` (สำเร็จ)
  - `401` / `403` (ถ้า route ถูกครอบด้วย auth/authorize ใน `routes/api.php`)

---

## 404 (Fallback)

- **Behavior:** เมื่อไม่พบ route ใน API จะตอบกลับ JSON
- **Response example (JSON):**

```json
{
  "message": "Not Found"
}
```

- **HTTP status codes ที่เป็นไปได้:**
  - `404`
