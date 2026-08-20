<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Adds standard defensive HTTP response headers.
     *
     * These are safe defaults for a server-rendered app (Blade + a small
     * JSON API under /api). If the frontend later needs to embed third-party
     * widgets/iframes or load assets from other origins, the CSP value below
     * will need to be loosened accordingly — it currently only allows
     * same-origin resources plus inline styles/scripts (Blade + Vite dev
     * server use inline styles in a few places), plus jsdelivr/Google Fonts
     * CDNs used for Bootstrap and web fonts.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0'); // deprecated header, explicitly disabled per OWASP guidance (CSP supersedes it)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Content-Security-Policy: same-origin by default, plus the CDNs
        // this app actually loads (Bootstrap/icons via jsdelivr, fonts via
        // Google Fonts).
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; ".
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