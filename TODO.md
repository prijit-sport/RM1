# TODO (ดำเนินการต่อจากการแก้ Middleware/Policy)

## 1) เพิ่ม coverage ฝั่ง API
- [x] เพิ่ม `tests/Feature/ApiSmokeTest.php` เพื่อทดสอบ `/api/login`, `/api/dashboard`, `/api/rooms`
- [ ] เพิ่ม test เพิ่มเติมสำหรับ Room/Guest CRUD (store/update/destroy)
- [ ] เพิ่ม test สำหรับ response shape และ validation errors

## 2) Audit/Logging ตอนปฏิเสธสิทธิ์
- [ ] เพิ่ม middleware/log เมื่อ authorization ถูก deny (เช่น log route, user_id, role)
- [ ] เพิ่ม test ว่าเมื่อ deny แล้วมีการเขียน log ตามที่กำหนด

## 3) Refactor ให้ policy-first
- [ ] ลด logic ซ้ำระหว่าง middleware และ policy
- [ ] ปรับให้ทุก route ใช้ policy เป็นแหล่งตัดสินใจเดียว

