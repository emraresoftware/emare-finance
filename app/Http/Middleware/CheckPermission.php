<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kullanıcının belirtilen yetkiye sahip olup olmadığını kontrol eder.
 *
 * Kullanım: Route::middleware('permission:sales.create')
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasPermission($permissionCode)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bu işlem için yetkiniz bulunmamaktadır.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'Bu işlem için yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}
