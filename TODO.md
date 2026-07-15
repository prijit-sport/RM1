# TODO - Role constants refactor (Policies)

- [ ] แก้ไฟล์ policies ทั้ง 4 ไฟล์ (InvoicePolicy, MaintenancePolicy, FacilityPolicy, MeterPolicy)
- [ ] ตรวจ/เพิ่ม use App\Models\Role ในทั้ง 4 ไฟล์
- [ ] แทนที่ hasRole('Admin') -> hasRole(Role::ADMIN) และ hasRole('Manager') -> hasRole(Role::MANAGER) (ตามที่ระบุ)
- [ ] รัน findstr เพื่อตรวจว่าไม่มี hasRole('Admin') หรือ hasRole('Manager') คงอยู่ในทั้ง 4 ไฟล์
- [ ] รัน php artisan test และยืนยันผล 78/78
- [ ] commit ด้วย message: refactor: apply Role constants to remaining policies
