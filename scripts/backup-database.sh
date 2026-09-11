#!/usr/bin/env bash
#
# scripts/backup-database.sh
#
# สคริปต์สำรองฐานข้อมูล MySQL สำหรับ Rm1 — ใช้ก่อน deploy/migrate ทุกครั้ง
# หรือตั้งเป็น cron รายวันก็ได้
#
# วิธีใช้ (manual):
#   bash scripts/backup-database.sh
#
# วิธีตั้ง cron รายวัน (ตัวอย่าง รันทุกวันตี 2):
#   crontab -e
#   0 2 * * * cd /path/to/Rm1 && bash scripts/backup-database.sh >> storage/logs/backup.log 2>&1
#
# อ่านค่า DB_* จากไฟล์ .env ของโปรเจกต์โดยตรง ไม่ต้อง hardcode credentials
# ในสคริปต์นี้ — ปลอดภัยกว่าและไม่ต้องแก้สคริปต์เวลาย้ายเซิร์ฟเวอร์
 
set -euo pipefail
 
# ─────────────────────────────────────────
#  ตั้งค่า
# ─────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"
BACKUP_DIR="$PROJECT_ROOT/storage/app/backups"
RETENTION_DAYS=30   # เก็บไฟล์ backup ไว้กี่วัน (ไฟล์เก่ากว่านี้จะถูกลบอัตโนมัติ)
 
if [ ! -f "$ENV_FILE" ]; then
    echo "❌ ไม่พบไฟล์ .env ที่ $ENV_FILE — ยกเลิกการ backup"
    exit 1
fi
 
# อ่านค่าจาก .env (รองรับกรณีมี/ไม่มีเครื่องหมายคำพูดรอบค่า)
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
 
if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo "❌ อ่านค่า DB_DATABASE หรือ DB_USERNAME จาก .env ไม่ได้ — ยกเลิกการ backup"
    exit 1
fi
 
mkdir -p "$BACKUP_DIR"
 
TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
BACKUP_FILE="$BACKUP_DIR/${DB_DATABASE}_${TIMESTAMP}.sql.gz"
 
# ─────────────────────────────────────────
#  สำรองข้อมูล
# ─────────────────────────────────────────
echo "📦 กำลังสำรองฐานข้อมูล '$DB_DATABASE' ..."
 
MYSQL_PWD="$DB_PASSWORD" mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    "$DB_DATABASE" | gzip > "$BACKUP_FILE"
 
if [ $? -ne 0 ] || [ ! -s "$BACKUP_FILE" ]; then
    echo "❌ Backup ล้มเหลว หรือไฟล์ที่ได้ว่างเปล่า — ตรวจสอบ credentials/สิทธิ์การเข้าถึง DB"
    rm -f "$BACKUP_FILE"
    exit 1
fi
 
BACKUP_SIZE="$(du -h "$BACKUP_FILE" | cut -f1)"
echo "✅ สำรองสำเร็จ: $BACKUP_FILE ($BACKUP_SIZE)"
 
# ─────────────────────────────────────────
#  ลบไฟล์ backup เก่าที่เกินระยะเวลาเก็บ
# ─────────────────────────────────────────
DELETED_COUNT="$(find "$BACKUP_DIR" -name "${DB_DATABASE}_*.sql.gz" -mtime +"$RETENTION_DAYS" -print -delete | wc -l)"
if [ "$DELETED_COUNT" -gt 0 ]; then
    echo "🗑️  ลบไฟล์ backup เก่ากว่า $RETENTION_DAYS วันไปแล้ว $DELETED_COUNT ไฟล์"
fi
 
echo "เสร็จสิ้น."
 