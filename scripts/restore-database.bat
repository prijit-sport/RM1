@echo off
REM ============================================================
REM  scripts\restore-database.bat
REM
REM  กู้คืนฐานข้อมูล MySQL ของ Rm1 จากไฟล์ backup ที่สร้างโดย
REM  backup-database.bat (เวอร์ชัน Windows .bat)
REM
REM  ⚠️ คำสั่งนี้จะ "เขียนทับข้อมูลปัจจุบันทั้งหมด" ในฐานข้อมูล — ใช้
REM  เฉพาะตอน rollback จริง ๆ เท่านั้น ไม่ใช่คำสั่งที่รันเล่น ๆ
REM
REM  วิธีใช้:
REM     scripts\restore-database.bat "storage\app\backups\room_management_20260101_020000.sql"
REM ============================================================

setlocal enabledelayedexpansion

set "SCRIPT_DIR=%~dp0"
set "PROJECT_ROOT=%SCRIPT_DIR%.."
set "ENV_FILE=%PROJECT_ROOT%\.env"
set "MYSQL_BIN=F:\xampp\mysql\bin"

if "%~1"=="" (
    echo วิธีใช้: scripts\restore-database.bat ^<path-to-backup.sql^>
    exit /b 1
)

set "BACKUP_FILE=%~1"

if not exist "%BACKUP_FILE%" (
    echo [ERROR] ไม่พบไฟล์ backup: %BACKUP_FILE%
    exit /b 1
)

if not exist "%ENV_FILE%" (
    echo [ERROR] ไม่พบไฟล์ .env ที่ %ENV_FILE% — ยกเลิก
    exit /b 1
)

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

echo.
echo คำสั่งนี้จะเขียนทับฐานข้อมูล "%DB_DATABASE%" บน %DB_HOST% ทั้งหมด
echo ด้วยข้อมูลจากไฟล์: %BACKUP_FILE%
echo.
set /p CONFIRM="พิมพ์ชื่อฐานข้อมูล (%DB_DATABASE%) เพื่อยืนยันว่าต้องการดำเนินการต่อ: "

if not "%CONFIRM%"=="%DB_DATABASE%" (
    echo [ERROR] ชื่อไม่ตรงกัน — ยกเลิกการกู้คืน ไม่มีข้อมูลถูกเปลี่ยนแปลง
    exit /b 1
)

echo กำลังกู้คืนฐานข้อมูล...

set "MYSQL_PWD=%DB_PASSWORD%"
"%MYSQL_BIN%\mysql.exe" --host="%DB_HOST%" --port="%DB_PORT%" --user="%DB_USERNAME%" "%DB_DATABASE%" < "%BACKUP_FILE%"
set "MYSQL_PWD="

if errorlevel 1 (
    echo [ERROR] กู้คืนล้มเหลว — ดูข้อความ error ด้านบน
    exit /b 1
)

echo [OK] กู้คืนสำเร็จจาก: %BACKUP_FILE%
echo.
echo ขั้นตอนถัดไปที่ควรทำ:
echo   1. php artisan config:clear ^&^& php artisan cache:clear
echo   2. เช็คว่า migration ของ code เวอร์ชันปัจจุบันตรงกับโครงสร้างฐานข้อมูลที่กู้คืนมา
echo   3. ทดสอบ login และหน้าเว็บหลักก่อนเปิดให้ผู้ใช้งานจริงเข้าใช้งานต่อ
