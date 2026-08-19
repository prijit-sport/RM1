# RM1 — Laravel Room & Billing System

ระบบจัดการห้องพัก การจอง ผู้เช่า สัญญา ใบแจ้งหนี้ มิเตอร์ และซ่อมบำรุง (Rental/Utility management)

คำอธิบายโปรเจค
จัดการข้อมูลห้อง (Rooms)
จัดการผู้เช่า (Guests)
จัดการการจอง/เช็คอิน/เช็คเอาท์ (Bookings)
จัดการสัญญา (Contracts)
จัดการใบแจ้งหนี้ (Invoices) แยกประเภทค่าเช่า / ค่าน้ำ-ค่าไฟ
คำนวณจากมิเตอร์ (Meter readings) เพื่อสร้าง/อัปเดตใบแจ้งหนี้ประเภท utility
จัดการซ่อมบำรุง (Maintenances)
แสดงรายงาน (Reports/ Dashboard)
ความต้องการของระบบ (System Requirements)
PHP >= 8.2
Laravel 12.x
MySQL (สำหรับ production) หรือใช้ SQLite (สำหรับทดสอบ)
Composer
Node.js + npm (สำหรับ build asset)

หมายเหตุ: ค่า config การรัน test ถูกตั้งไว้ใน phpunit.xml ให้ใช้ sqlite ในหน่วยความจำ (:memory:)

ขั้นตอนติดตั้ง (Installation)

แนะนำให้ใช้ขั้นตอนนี้บนเครื่องเดียวกับที่ต้องการรัน dev/test

Clone โปรเจค
ติดตั้ง Dependencies
bash
   composer install
เตรียมไฟล์ .env
ถ้าไม่มี .env ให้คัดลอกจากตัวอย่าง
bash
     copy .env.example .env
ตั้งค่าฐานข้อมูลใน .env (เช่น DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
สร้าง key
bash
   php artisan key:generate
รัน migration
bash
   php artisan migrate
(ถ้าต้องการ) รัน seed
bash
   php artisan db:seed
ติดตั้ง/สร้าง asset
bash
   npm install
   npm run build
การรัน dev server (Development)
bash
php artisan serve

ถ้าต้องการรันแบบที่รวม queue/logs/vite ตามที่ระบุใน composer script:

bash
composer run dev
วิธีรัน Unit/Feature Test

รันทุก test:

bash
php artisan test

โปรเจคมีชุดทดสอบใน tests/Unit และ tests/Feature และ phpunit.xml ถูกตั้งค่า environment สำหรับ test (เช่น APP_ENV=testing, DB_CONNECTION=sqlite, SESSION_DRIVER=array)

โครงสร้างไฟล์ที่เกี่ยวข้อง
Routes (Web): routes/web.php
Routes (API): routes/api.php
Controller: app/Http/Controllers/
Request validation: app/Http/Requests/
Models: app/Models/
Test: tests/
หมายเหตุด้านความปลอดภัย
route บางส่วนถูกจำกัดด้วย middleware เช่น auth, admin_only, manager_or_admin ตามที่กำหนดใน routes/web.php
License

MIT (ตามที่ระบุใน composer.json)

การเตรียมพร้อมสำหรับ Production
ต้องเปลี่ยน APP_ENV=production และ APP_DEBUG=false ใน .env จริงก่อน deploy เสมอ
แนะนำตั้งค่า QUEUE_CONNECTION เป็น database หรือ redis (ปัจจุบันใช้ database อยู่แล้ว)
ต้องรัน php artisan config:cache, route:cache, view:cache หลัง deploy
ต้อง backup ฐานข้อมูลด้วย mysqldump ก่อนรัน migration บน production เสมอ
File Permission สำหรับ storage/ และ bootstrap/cache/

Laravel ต้องการสิทธิ์การเขียน (write permission) ที่โฟลเดอร์ storage/ และ bootstrap/cache/ เสมอ ไม่ว่าจะ deploy บนสภาพแวดล้อมแบบใดก็ตาม เพราะทั้งสองโฟลเดอร์นี้ถูกใช้เก็บ:

Log ของแอปพลิเคชัน (storage/logs/)
ไฟล์ session (storage/framework/sessions/)
Compiled Blade view cache (storage/framework/views/)
Application cache เมื่อใช้ CACHE_STORE=file (storage/framework/cache/)
Bootstrap cache สำหรับ config:cache, route:cache (bootstrap/cache/)

หากลืมตั้งค่า permission ให้ถูกต้องหลัง deploy บนเซิร์ฟเวอร์ใหม่ แอปจะขึ้น error ทันทีตั้งแต่ ครั้งแรกที่มีคนเข้าใช้งาน (เช่น The stream or file "storage/logs/laravel.log" could not be opened)

สำหรับ Linux/Unix server (VPS, dedicated server):

bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

หมายเหตุ: user ของ web server อาจแตกต่างกันไปตามสภาพแวดล้อมที่ใช้จริง เช่น www-data (Apache/Nginx บน Debian/Ubuntu), nginx หรือ apache (บางระบบที่ใช้ CentOS/RHEL), หรือ sail/herd (ถ้าใช้ Laravel Sail หรือ Herd) — ให้ตรวจสอบ user ที่ web server process รันอยู่จริงก่อนตั้งค่า chown (เช่น ps aux | grep -E 'nginx|apache|php-fpm')

สำหรับ Shared Hosting (cPanel หรือใกล้เคียง):

โดยทั่วไป shared hosting ไม่อนุญาตให้รันคำสั่ง chmod/chown ผ่าน SSH ได้โดยตรง (หรือไม่มี SSH access เลย) ให้ตั้งค่า permission ผ่าน File Manager ของ cPanel แทน:

เข้า File Manager แล้วนำทางไปที่โฟลเดอร์โปรเจค
คลิกขวาที่โฟลเดอร์ storage และ bootstrap/cache เลือก Permissions (หรือ Change Permissions)
ตั้งค่าเป็น 755 หรือ 775 (ขึ้นอยู่กับการตั้งค่าของผู้ให้บริการ hosting แต่ละราย) และเลือก Recurse into subdirectories เพื่อให้มีผลกับไฟล์/โฟลเดอร์ย่อยทั้งหมดด้วย
หากยังพบปัญหา permission denied ให้ติดต่อฝ่าย support ของผู้ให้บริการ hosting เพื่อยืนยัน user/group ที่ PHP process รันอยู่
ตรวจสอบความพร้อมก่อน deploy (แนะนำ)

รันคำสั่งต่อไปนี้ ก่อนทุกครั้งที่ deploy เพื่อตรวจสอบว่าค่า config/env ปลอดภัยสำหรับ production:

bash
php artisan app:check-production-readiness

คำสั่งนี้จะตรวจสอบ 6 รายการ และแสดงเป็นตาราง (✅/❌):

APP_ENV ต้องเป็น production
APP_DEBUG ต้องเป็น false
SESSION_SECURE_COOKIE ต้องเป็น true (บังคับใช้ HTTPS cookie)
APP_KEY ต้องไม่ว่างเปล่า และต้องขึ้นต้นด้วย base64: (จาก php artisan key:generate)
APP_URL ต้องไม่ใช่ค่า default

LOG_LEVEL ต้องไม่เป็น debug ใน production

หากมีรายการใดไม่ผ่าน command จะคืนค่า exit code ที่ไม่ใช่ 0 (ล้มเหลว) และถ้าผ่านครบทุกรายการจะคืนค่า 0 (สำเร็จ) — จึงสามารถใช้เป็นเงื่อนไขใน CI/CD หรือ deploy script ได้ เช่น:

bash
php artisan app:check-production-readiness || exit 1

หมายเหตุ: ควรใช้คำสั่งนี้บนค่า config ที่ถูก cache แล้ว (หลัง php artisan config:cache) เพื่อให้ตรงกับค่าที่ app ใช้จริงใน production
