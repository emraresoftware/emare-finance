<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Belirtilen modülün aktif olup olmadığını kontrol eder.
 *
 * Kullanım: Route::middleware('module:hardware')
 */
class CheckModule
{
    public function __construct(
        private ModuleService $moduleService,
    ) {}

    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        $user   = $request->user();

        // Super admin tüm modüllere erişebilir
        if ($user && $user->is_super_admin) {
            return $next($request);
        }

        $branch = $user?->branch;

        if (!$this->moduleService->isActive($moduleCode, $branch)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "'{$moduleCode}' modülü aktif değildir.",
                ], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, "'{$moduleCode}' modülü aktif değildir.");
        }

        return $next($request);
    }
}
