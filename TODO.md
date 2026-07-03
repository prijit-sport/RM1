# TODO

- [ ] ลบ route /reports ที่ประกาศซ้ำชุดเก่าออก (routes/web.php บรรทัดในกลุ่ม middleware('auth') ทั่วไป) เหลือเฉพาะชุดในกลุ่ม middleware(['auth','manager_or_admin']).
- [ ] รัน `php artisan route:list --name=reports` ตรวจว่ามี 4 routes (reports.index/export/revenue/occupancy) และ middleware แสดง `manager_or_admin`.
- [ ] รัน `php artisan test --filter=ReportControllerTest` ตรวจว่ายังผ่าน.
- [ ] รัน `php artisan test` ทั้งโปรเจค ยืนยันว่าไม่มี fail (78/78).
