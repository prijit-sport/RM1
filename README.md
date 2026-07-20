# RM1 — Laravel Room & Billing System

ระบบจัดการห้องพัก การจอง ผู้เช่า สัญญา ใบแจ้งหนี้ มิเตอร์ และซ่อมบำรุง (Rental/Utility management)

## คำอธิบายโปรเจค

- จัดการข้อมูลห้อง (Rooms)
- จัดการผู้เช่า (Guests)
- จัดการการจอง/เช็คอิน/เช็คเอาท์ (Bookings)
- จัดการสัญญา (Contracts)
- จัดการใบแจ้งหนี้ (Invoices) แยกประเภทค่าเช่า / ค่าน้ำ-ค่าไฟ
- คำนวณจากมิเตอร์ (Meter readings) เพื่อสร้าง/อัปเดตใบแจ้งหนี้ประเภท utility
- จัดการซ่อมบำรุง (Maintenances)
- แสดงรายงาน (Reports/ Dashboard)

## ความต้องการของระบบ (System Requirements)

- PHP **>= 8.2**
- Laravel **12.x**
- MySQL (สำหรับ production) หรือใช้ SQLite (สำหรับทดสอบ)
- Composer
- Node.js + npm (สำหรับ build asset)

> หมายเหตุ: ค่า config การรัน test ถูกตั้งไว้ใน `phpunit.xml` ให้ใช้ `sqlite` ในหน่วยความจำ (`:memory:`)

## ขั้นตอนติดตั้ง (Installation)
>
> แนะนำให้ใช้ขั้นตอนนี้บนเครื่องเดียวกับที่ต้องการรัน dev/test

1. Clone โปรเจค
2. ติดตั้ง Dependencies

   ```bash
   composer install
   ```

3. เตรียมไฟล์ `.env`
   - ถ้าไม่มี `.env` ให้คัดลอกจากตัวอย่าง

     ```bash
     copy .env.example .env
     ```

   - ตั้งค่าฐานข้อมูลใน `.env` (เช่น DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
4. สร้าง key

   ```bash
   php artisan key:generate
   ```

5. รัน migration

   ```bash
   php artisan migrate
   ```

6. (ถ้าต้องการ) รัน seed

   ```bash
   php artisan db:seed
   ```

7. ติดตั้ง/สร้าง asset

   ```bash
   npm install
   npm run build
   ```

## การรัน dev server (Development)

```bash
php artisan serve
```

ถ้าต้องการรันแบบที่รวม queue/logs/vite ตามที่ระบุใน composer script:

```bash
composer run dev
```

## วิธีรัน Unit/Feature Test

รันทุก test:

```bash
php artisan test
```

> โปรเจคมีชุดทดสอบใน `tests/Unit` และ `tests/Feature` และ `phpunit.xml` ถูกตั้งค่า environment สำหรับ test (เช่น `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `SESSION_DRIVER=array`)

## โครงสร้างไฟล์ที่เกี่ยวข้อง

- Routes (Web): `routes/web.php`
- Routes (API): `routes/api.php`
- Controller: `app/Http/Controllers/`
- Request validation: `app/Http/Requests/`
- Models: `app/Models/`
- Test: `tests/`

## หมายเหตุด้านความปลอดภัย

- route บางส่วนถูกจำกัดด้วย middleware เช่น `auth`, `admin_only`, `manager_or_admin` ตามที่กำหนดใน `routes/web.php`

## License

MIT (ตามที่ระบุใน `composer.json`)

## การเตรียมพร้อมสำหรับ Production

- ต้องเปลี่ยน APP_ENV=production และ APP_DEBUG=false ใน .env จริงก่อน deploy เสมอ
- แนะนำตั้งค่า QUEUE_CONNECTION เป็น database หรือ redis (ปัจจุบันใช้ database อยู่แล้ว)
- ต้องรัน php artisan config:cache, route:cache, view:cache หลัง deploy
- ต้อง backup ฐานข้อมูลด้วย mysqldump ก่อนรัน migration บน production เสมอ
- Invoice PDF generation (route invoices.pdf) ยังเป็น placeholder ที่ redirect
  กลับพร้อมข้อความ coming soon ยังไม่ได้ implement จริง
