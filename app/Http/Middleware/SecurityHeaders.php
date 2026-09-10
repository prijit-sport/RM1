<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Adds standard defensive HTTP response headers.
     *
     * These are safe defaults for a server-rendered app (Blade + a small
     * JSON API under /api). script-src uses a per-request CSP nonce
     * (see $cspNonce below) instead of 'unsafe-inline' — every inline
     * <script> block in the Blade views must carry nonce="{{ $cspNonce }}"
     * or the browser will refuse to run it. style-src still allows
     * 'unsafe-inline' (Blade + Vite dev server use inline styles in a few
     * places) plus jsdelivr/Google Fonts CDNs used for Bootstrap and web
     * fonts.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ FIX (CSP nonce-based): เดิม script-src ใช้ 'unsafe-inline' ซึ่ง
        // แทบไม่ช่วยป้องกัน XSS เลยสำหรับ inline <script> — สร้าง nonce สุ่ม
        // ใหม่ทุก request แล้วแชร์ให้ทุก view ผ่าน View::share() เพื่อให้ Blade
        // ใส่ nonce="{{ $cspNonce }}" ใน <script> tag ของตัวเองได้
        $nonce = base64_encode(Str::random(24));
        View::share('cspNonce', $nonce);

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0'); // deprecated header, explicitly disabled per OWASP guidance (CSP supersedes it)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Content-Security-Policy: same-origin by default, plus the CDNs
        // this app actually loads (Bootstrap/icons via jsdelivr, fonts via
        // Google Fonts). script-src uses the per-request nonce instead of
        // 'unsafe-inline' — browsers that understand 'nonce-...' ignore
        // 'unsafe-inline' automatically per the CSP3 spec, so dropping it
        // here is both correct and intentional, not an oversight.
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'nonce-{$nonce}' https://cdn.jsdelivr.net; ".
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; ".
            "img-src 'self' data:; ".
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net; ".
            "connect-src 'self' https://cdn.jsdelivr.net; ".
            "frame-ancestors 'none'; ".
            "base-uri 'self'; ".
            "form-action 'self'"
        );

        // Only send HSTS over an actual HTTPS connection, and only once the
        // app is confirmed to run on HTTPS in production (avoid locking
        // browsers into HTTPS during local/http development).
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
