<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Check if 2FA is required for sensitive modules
        $sensitiveModules = ['finance', 'hr', 'procurement'];
        $currentModule = $this->extractModule($request->path());

        if (in_array($currentModule, $sensitiveModules) && $user->two_factor_enabled) {
            // Check if 2FA session is verified
            if (!session('2fa_verified')) {
                return response()->json([
                    'message' => 'Two-factor authentication required',
                    'requires_2fa' => true
                ], 403);
            }
        }

        return $next($request);
    }

    private function extractModule(string $path): string
    {
        $parts = explode('/', $path);
        return $parts[1] ?? 'unknown';
    }
}

