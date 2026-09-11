#!/usr/bin/env bash
#
# scripts/restore-database.sh
#
# กู้คืนฐานข้อมูล MySQL ของ Rm1 จากไฟล์ backup ที่สร้างโดย backup-database.sh
#
# ⚠️ คำสั่งนี้จะ "เขียนทับข้อมูลปัจจุบันทั้งหมด" ในฐานข้อมูล — ใช้เฉพาะตอน
# rollback จริง ๆ เท่านั้น ไม่ใช่คำสั่งที่รันเล่น ๆ
#
# วิธีใช้:
#   bash scripts/restore-database.sh storage/app/backups/room_management_20260101_020000.sql.gz
#
# สคริปต์จะถามยืนยันก่อนเขียนทับเสมอ (ไม่มี flag ข้ามการยืนยัน โดยตั้งใจ)
 
set -euo pipefail
 
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"
 
if [ $# -ne 1 ]; then
    echo "วิธีใช้: bash scripts/restore-database.sh <path-to-backup.sql.gz>"
    exit 1
fi
 
BACKUP_FILE="$1"
 
if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ ไม่พบไฟล์ backup: $BACKUP_FILE"
    exit 1
fi
 
if [ ! -f "$ENV_FILE" ]; then
    echo "❌ ไม่พบไฟล์ .env ที่ $ENV_FILE — ยกเลิก"
    exit 1
fi
 
get_env() {
    grep -E "^${1}=" "$ENV_FILE" | tail -n1 | cut -d '=' -f2- | sed -e 's/^"//' -e 's/"$//'
}
 
DB_HOST="$(get_env DB_HOST)"
DB_PORT="$(get_env DB_PORT)"
DB_DATABASE="$(get_env DB_DATABASE)"
DB_USERNAME="$(get_env DB_USERNAME)"
DB_PASSWORD="$(get_env DB_PASSWORD)"
 
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
 
echo "⚠️  คำสั่งนี้จะเขียนทับฐานข้อมูล '$DB_DATABASE' บน $DB_HOST ทั้งหมด"
echo "    ด้วยข้อมูลจากไฟล์: $BACKUP_FILE"
read -r -p "พิมพ์ชื่อฐานข้อมูล ($DB_DATABASE) เพื่อยืนยันว่าต้องการดำเนินการต่อ: " CONFIRM
 
if [ "$CONFIRM" != "$DB_DATABASE" ]; then
    echo "❌ ชื่อไม่ตรงกัน — ยกเลิกการกู้คืน (ไม่มีข้อมูลถูกเปลี่ยนแปลง)"
    exit 1
fi
 
echo "📦 กำลังกู้คืนฐานข้อมูล..."
 
gunzip -c "$BACKUP_FILE" | MYSQL_PWD="$DB_PASSWORD" mysql \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    "$DB_DATABASE"
 
echo "✅ กู้คืนสำเร็จจาก: $BACKUP_FILE"
echo ""
echo "ขั้นตอนถัดไปที่ควรทำ:"
echo "  1. php artisan config:clear && php artisan cache:clear"
echo "  2. เช็คว่า migration ของ code เวอร์ชันปัจจุบันตรงกับโครงสร้างฐานข้อมูลที่กู้คืนมา"
echo "     (ถ้า rollback code ไปเวอร์ชันเก่ากว่าด้วย ให้ rollback ก่อน restore เสมอ)"
echo "  3. ทดสอบ login และหน้าเว็บหลักก่อนเปิดให้ผู้ใช้งานจริงเข้าใช้งานต่อ"
 