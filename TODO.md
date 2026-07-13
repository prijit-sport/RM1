# TODO

- [x] ลบ route /reports ที่ประกาศซ้ำชุดเก่าออก (routes/web.php)
- [x] งาน 1: ย้าย ReportService ให้คำนวณด้วย SQL aggregation แทน in-memory
- [ ] งาน 2: เพิ่ม Caching สำหรับข้อมูลที่เปลี่ยนไม่บ่อย (roles/permissions, facilities) + cache invalidation + ตรวจ test ผ่าน 78/78
