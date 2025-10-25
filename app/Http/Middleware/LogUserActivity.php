<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            // Log API access
            $this->auditService->log(
                $request->user(),
                $request->method(),
                $this->extractModule($request->path()),
                null,
                null,
                [
                    'path' => $request->path(),
                    'method' => $request->method(),
                ]
            );
        }

        return $response;
    }

    private function extractModule(string $path): string
    {
        $parts = explode('/', $path);
        return $parts[1] ?? 'unknown';
    }
}
