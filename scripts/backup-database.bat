@echo off
REM ============================================================
REM  scripts\backup-database.bat
REM
REM  สคริปต์สำรองฐานข้อมูล MySQL สำหรับ Rm1 (เวอร์ชัน Windows .bat
REM  ใช้แทน backup-database.sh เพราะเครื่องนี้ไม่มี bash ติดตั้งไว้)
REM
REM  วิธีใช้ (manual): ดับเบิลคลิกไฟล์นี้ หรือรันจาก cmd
REM     scripts\backup-database.bat
REM
REM  วิธีตั้งอัตโนมัติ: ใช้ Windows Task Scheduler ตั้งให้รันไฟล์นี้
REM  ทุกวัน (ดูขั้นตอนในคอมเมนต์ท้ายไฟล์)
REM ============================================================

setlocal enabledelayedexpansion

REM ── ตั้งค่า ──────────────────────────────────────────────
set "SCRIPT_DIR=%~dp0"
set "PROJECT_ROOT=%SCRIPT_DIR%.."
set "ENV_FILE=%PROJECT_ROOT%\.env"
set "BACKUP_DIR=%PROJECT_ROOT%\storage\app\backups"
set "MYSQL_BIN=F:\xampp\mysql\bin"
set "RETENTION_DAYS=30"

if not exist "%ENV_FILE%" (
    echo [ERROR] ไม่พบไฟล์ .env ที่ %ENV_FILE% — ยกเลิกการ backup
    exit /b 1
)

REM ── อ่านค่า DB_* จาก .env ────────────────────────────────
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_DATABASE="
set "DB_USERNAME="
set "DB_PASSWORD="

for /f "usebackq eol=# tokens=1,* delims==" %%A in ("%ENV_FILE%") do (
    if "%%A"=="DB_HOST" set "DB_HOST=%%B"
    if "%%A"=="DB_PORT" set "DB_PORT=%%B"
    if "%%A"=="DB_DATABASE" set "DB_DATABASE=%%B"
    if "%%A"=="DB_USERNAME" set "DB_USERNAME=%%B"
    if "%%A"=="DB_PASSWORD" set "DB_PASSWORD=%%B"
)

if "%DB_DATABASE%"=="" (
    echo [ERROR] อ่านค่า DB_DATABASE จาก .env ไม่ได้ — ยกเลิกการ backup
    exit /b 1
)

if "%DB_USERNAME%"=="" (
    echo [ERROR] อ่านค่า DB_USERNAME จาก .env ไม่ได้ — ยกเลิกการ backup
    exit /b 1
)

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM ── สร้างชื่อไฟล์ตาม timestamp (ใช้ PowerShell เพื่อไม่ให้ขึ้นกับ locale ของเครื่อง) ──
for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"') do set "TIMESTAMP=%%i"
set "BACKUP_FILE=%BACKUP_DIR%\%DB_DATABASE%_%TIMESTAMP%.sql"

echo กำลังสำรองฐานข้อมูล "%DB_DATABASE%" ...

REM ── สำรองข้อมูล (ใช้ MYSQL_PWD env var แทนใส่ password ใน argument
REM     โดยตรง ป้องกัน password โผล่ใน process list) ──
set "MYSQL_PWD=%DB_PASSWORD%"
"%MYSQL_BIN%\mysqldump.exe" --host="%DB_HOST%" --port="%DB_PORT%" --user="%DB_USERNAME%" --single-transaction --quick --routines --triggers "%DB_DATABASE%" > "%BACKUP_FILE%"
set "MYSQL_PWD="

if not exist "%BACKUP_FILE%" (
    echo [ERROR] Backup ล้มเหลว — ไม่พบไฟล์ผลลัพธ์
    exit /b 1
)

for %%A in ("%BACKUP_FILE%") do set "FILESIZE=%%~zA"
if "%FILESIZE%"=="0" (
    echo [ERROR] Backup ล้มเหลว — ไฟล์ที่ได้ว่างเปล่า ตรวจสอบ credentials/สิทธิ์การเข้าถึง DB
    del "%BACKUP_FILE%"
    exit /b 1
)

echo [OK] สำรองสำเร็จ: %BACKUP_FILE% (%FILESIZE% bytes)

REM ── ลบไฟล์ backup เก่าที่เกินระยะเวลาเก็บ ──────────────────
forfiles /p "%BACKUP_DIR%" /m "%DB_DATABASE%_*.sql" /d -%RETENTION_DAYS% /c "cmd /c del @path" 2>nul

echo เสร็จสิ้น.
exit /b 0

REM ============================================================
REM  วิธีตั้งเป็น Task Scheduler รันอัตโนมัติทุกวัน:
REM
REM  1. เปิด Task Scheduler (พิมพ์ "Task Scheduler" ใน Start menu)
REM  2. คลิก "Create Basic Task..." ทางขวา
REM  3. ตั้งชื่อ เช่น "Rm1 Daily Backup" กด Next
REM  4. เลือก Trigger เป็น "Daily" กด Next แล้วตั้งเวลา เช่น 02:00
REM  5. เลือก Action เป็น "Start a program" กด Next
REM  6. ช่อง "Program/script" ใส่ path เต็มของไฟล์นี้ เช่น:
REM       F:\xampp\htdocs\Rm1\scripts\backup-database.bat
REM  7. ช่อง "Start in (optional)" ใส่:
REM       F:\xampp\htdocs\Rm1\scripts
REM  8. กด Next แล้ว Finish
REM  9. ทดสอบ: คลิกขวาที่ task ที่สร้าง เลือก "Run" แล้วเช็คว่ามีไฟล์
REM     .sql ใหม่โผล่มาที่ storage\app\backups\ หรือไม่
REM ============================================================
