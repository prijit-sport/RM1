# TODO

## ข้อ 1: Refactor InvoiceController.bulkCreate()/bulkStore()

- [ ] 1.1 ย้ายเมธอด utility meter calculation และ helper จาก InvoiceController ไปไว้ใน InvoiceService
- [ ] 1.2 เพิ่ม public methods ใน InvoiceService เพื่อรองรับการทำงานของ bulkCreate/bulkStore (utility+rent)
- [ ] 1.3 ปรับ InvoiceController ให้เหลือการเรียก Service และ return/redirect เท่านั้น
- [ ] 1.4 ลบ helper ใน InvoiceController ที่ถูกย้ายแล้ว
- [ ] 1.5 รัน `php artisan test` เพื่อตรวจ 77 passed
- [ ] 1.6 ถ้า test pass: commit + push (message: "refactor: move bulkCreate/bulkStore logic to InvoiceService")
