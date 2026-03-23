<?php

namespace App\Http\Middleware;

use App\Models\AdminAuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuditLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
            $payload = $this->sanitizePayload($request->except(['_token', '_method']));

            AdminAuditLog::create([
                'user_id' => auth()->id(),
                'method' => strtoupper($request->method()),
                'path' => (string) $request->path(),
                'route_name' => optional($request->route())->getName(),
                'status_code' => $response->getStatusCode(),
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
                'payload' => $payload ?: null,
            ]);
        }

        return $response;
    }

    private function sanitizePayload(array $payload): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'api_token', 'secret', 'token'];

        array_walk_recursive($payload, function (&$value, $key) use ($sensitiveKeys): void {
            $keyLower = strtolower((string) $key);
            foreach ($sensitiveKeys as $sensitive) {
                if (str_contains($keyLower, $sensitive)) {
                    $value = '[REDACTED]';
                    return;
                }
            }

            if (is_string($value) && mb_strlen($value) > 400) {
                $value = mb_substr($value, 0, 400) . '...';
            }
        });

        return $payload;
    }
}
