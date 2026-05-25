# TODO (ดำเนินการต่อจากการแก้ Middleware/Policy)

## 1) เพิ่ม coverage ฝั่ง API

- [x] เพิ่ม `tests/Feature/ApiSmokeTest.php` เพื่อทดสอบ `/api/login`, `/api/dashboard`, `/api/rooms`

- [ ] เพิ่ม test เพิ่มเติมสำหรับ Room/Guest CRUD (store/update/destroy)

- [ ] เพิ่ม test สำหรับ response shape และ validation errors

## 2) Audit/Logging ตอนปฏิเสธสิทธิ์

- [x] เพิ่ม middleware/log เมื่อ authorization ถูก deny (เช่น log route, user_id, role)

- [x] เพิ่ม test ว่าเมื่อ deny แล้วมีการเขียน log ตามที่กำหนด

## 3) Refactor ให้ policy-first

- [x] ลด logic ซ้ำระหว่าง middleware และ policy

- [x] ปรับให้ทุก route ใช้ policy เป็นแหล่งตัดสินใจเดียว

## 4) Checklist ก่อน deploy

- [ ] รัน `php artisan test`

- [ ] ตรวจสอบ `.env` และค่าคอนฟิกสำหรับ staging/production

- [ ] ตรวจสอบ migration/seed ที่ต้องรันใน deploy

- [ ] ตรวจสอบ key/app config (`php artisan key:generate` ถ้าจำเป็น)

- [ ] ตรวจสอบ storage/link และ permission ที่จำเป็น

- [ ] ตรวจสอบ queue/scheduler/cron

- [ ] ตรวจสอบการตั้งค่า mail, storage, และ external services

- [ ] ตรวจสอบ log level และ error reporting ใน production

- [ ] สร้าง backup ก่อน deploy

- [ ] ทำ smoke test หลัง deploy

## 5) ขั้นตอน QA เพิ่มเติมหลัง deploy

- [ ] ทดสอบการ login และ token API

- [ ] ทดสอบ dashboard API

- [ ] ทดสอบการสร้าง/แก้ไข/ลบ Room และ Guest (ถ้ามี endpoint)

- [ ] ทดสอบการคำนวณค่ามิเตอร์และ invoice

- [ ] ทดสอบการจ่าย invoice และสถานะที่อัปเดต

- [ ] ทดสอบการเข้าถึงตาม role และ deny logging

- [ ] ทดสอบ UI/หน้าที่เกี่ยวข้องกับ module หลัก

- [ ] ตรวจสอบ logs และ error หลัง deploy 15-30 นาที

- [ ] เก็บผล QA และ issue ที่พบไว้เป็นบันทึก
