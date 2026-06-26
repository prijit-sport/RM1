<?php
namespace App\Support;
 
class DirectLog
{
    public static function write(string $message): void
    {
        // ✅ เขียนตรงไปที่ storage/logs/laravel.log เสมอ
        // เพื่อให้ test อ่านได้จาก storage_path('logs/laravel.log')
        $file = storage_path('logs/laravel.log');
        file_put_contents($file, $message . PHP_EOL, FILE_APPEND);
    }
}
 