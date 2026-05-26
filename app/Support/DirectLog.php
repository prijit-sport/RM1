<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class DirectLog
{
    public static function write(string $message): void
    {
        $path = 'logs/laravel.log';

        // Ensure storage disk exists; fall back to local file if Storage is not configured.
        try {
            Storage::disk('local')->append($path, $message . PHP_EOL);
        } catch (\Throwable $e) {
            $file = storage_path('logs/laravel.log');
            file_put_contents($file, $message . PHP_EOL, FILE_APPEND);
        }
    }
}
