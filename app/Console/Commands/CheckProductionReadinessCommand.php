<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
 
class CheckProductionReadinessCommand extends Command
{
    /** @var string */
    protected $signature = 'app:check-production-readiness';
 
    /** @var string */
    protected $description = 'Check that the current environment is safe to deploy to production';
 
    /**
     * The default APP_URL value from .env.example (dev default that must not be deployed).
     */
    private const DEFAULT_DEV_APP_URL = 'http://localhost:8000';
 
    public function handle(): int
    {
        $row = function (string $check, mixed $current): array {
            return [$check, '❌', (string) $current];
        };
 
        $passing = function (array &$rows, string $check, mixed $current): void {
            $rows[] = [$check, '✅', (string) $current];
        };
 
        $rows = [];
        $allPass = true;
 
        // 1. APP_ENV must be production
        $env = (string) config('app.env');
        if ($env === 'production') {
            $passing($rows, 'APP_ENV', $env);
        } else {
            $rows[] = $row('APP_ENV', $env);
            $allPass = false;
        }
 
        // 2. APP_DEBUG must be false
        $debug = config('app.debug');
        if ($debug === false) {
            $passing($rows, 'APP_DEBUG', $debug ? 'true' : 'false');
        } else {
            $rows[] = $row('APP_DEBUG', $debug ? 'true' : 'false');
            $allPass = false;
        }
 
        // 3. SESSION_SECURE_COOKIE must be true
        $secureCookie = config('session.secure');
        if ($secureCookie === true) {
            $passing($rows, 'SESSION_SECURE_COOKIE', $secureCookie ? 'true' : 'false');
        } else {
            $rows[] = $row('SESSION_SECURE_COOKIE', $secureCookie ? 'true' : 'false');
            $allPass = false;
        }
 
        // 4. APP_KEY must be set and be a base64: key
        $key = (string) config('app.key');
        if ($key !== '' && str_starts_with($key, 'base64:')) {
            $passing($rows, 'APP_KEY', 'base64:...');
        } elseif ($key === '') {
            $rows[] = $row('APP_KEY', '(empty)');
            $allPass = false;
        } else {
            $rows[] = $row('APP_KEY', '(not base64:)');
            $allPass = false;
        }
 
        // 5. APP_URL must not be the dev default, localhost, or a private/loopback IP.
        // (Previously this only rejected an exact match on the known dev default,
        // so an internal IP or forgotten localhost value would silently pass.)
        $appUrl = (string) config('app.url');
        $appUrlHost = $appUrl !== '' ? (parse_url($appUrl, PHP_URL_HOST) ?: '') : '';
        $isLocalHost = in_array($appUrlHost, ['localhost', '127.0.0.1', '::1'], true);
        $isValidIp = $appUrlHost !== '' && filter_var($appUrlHost, FILTER_VALIDATE_IP) !== false;
        $isPrivateOrReservedIp = $isValidIp && filter_var(
            $appUrlHost,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
 
        if ($appUrl !== '' && $appUrl !== self::DEFAULT_DEV_APP_URL && ! $isLocalHost && ! $isPrivateOrReservedIp) {
            $passing($rows, 'APP_URL', $appUrl);
        } else {
            $rows[] = $row('APP_URL', $appUrl !== '' ? $appUrl : '(empty)');
            $allPass = false;
        }
 
        // 6. LOG_LEVEL must not be debug in production
        $logLevel = (string) config('logging.channels.single.level', 'debug');
        if ($logLevel !== '' && strtolower($logLevel) !== 'debug') {
            $passing($rows, 'LOG_LEVEL', $logLevel);
        } else {
            $rows[] = $row('LOG_LEVEL', $logLevel !== '' ? $logLevel : '(empty)');
            $allPass = false;
        }
 
        $this->table(['Check', 'Status', 'Current Value'], $rows);
 
        if (! $allPass) {
            $this->error('Production readiness check FAILED — fix the ❌ items above before deploying.');
 
            return self::FAILURE;
        }
 
        $this->info('Production readiness check PASSED — safe to deploy.');
 
        return self::SUCCESS;
    }
}
 