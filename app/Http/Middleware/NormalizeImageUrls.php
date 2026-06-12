<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeImageUrls
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process JSON or text responses
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && (str_contains($contentType, 'application/json') || str_contains($contentType, 'text/plain'))) {
            $content = $response->getContent();
            if (is_string($content) && !empty($content)) {
                $schemeAndHttpHost = $request->schemeAndHttpHost();
                $hostStandard = $schemeAndHttpHost;
                $hostEscaped = str_replace('/', '\/', $schemeAndHttpHost);

                // 1. Replace any absolute localhost/127.0.0.1 base URLs (with/without port, standard/escaped)
                // Use regex to avoid double-ports (e.g. replacing http://127.0.0.1 with http://127.0.0.1:8000 when port is already there)
                $content = preg_replace('#https?://(?:localhost|127\.0\.0\.1)(?::\d+)?#i', $hostStandard, $content);
                $content = preg_replace('#https?:\\\\/\\\\/(?:localhost|127\.0\.0\.1)(?::\d+)?#i', $hostEscaped, $content);

                // 2. Normalize standard relative paths (e.g. "uploads/product/..." or "/uploads/product/...")
                $content = preg_replace(
                    '/"\/?(uploads|assets|placeholder\.jpg|placeholder\.png)([\/"])/',
                    '"' . $hostStandard . '/$1$2',
                    $content
                );

                // 3. Normalize JSON-escaped relative paths (e.g. "uploads\/product\/..." or "\/uploads\/product\/...")
                $content = preg_replace(
                    '/"(?:\\\\?\/)?(uploads|assets)\\\\/',
                    '"' . $hostEscaped . '\/$1\\',
                    $content
                );

                // 4. Normalize JSON-escaped placeholders (e.g. "\/placeholder.jpg")
                $content = preg_replace(
                    '/"\\\\?\/placeholder\.(jpg|png|jpeg|webp)"/',
                    '"' . $hostEscaped . '\/placeholder.$1"',
                    $content
                );

                $response->setContent($content);
            }
        }

        return $response;
    }
}
