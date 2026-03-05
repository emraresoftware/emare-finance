<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sadece Süper Admin kullanıcıların erişebileceği sayfalar için middleware.
 */
class SuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bu alana erişim yetkiniz bulunmamaktadır.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'Bu alana erişim yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}
