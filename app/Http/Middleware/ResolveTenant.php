<?php

namespace App\Http\Middleware;

use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Oturum açmış kullanıcının tenant bağlamını resolve eder.
 * Her istekte TenantContext singleton'ını mevcut kullanıcının tenant'ına ayarlar.
 */
class ResolveTenant
{
    public function __construct(
        private TenantContext $tenantContext,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->tenant) {
            $this->tenantContext->setTenant($user->tenant);

            // Tenant aktif değilse erişimi engelle
            if (!$user->tenant->isActive() && !$user->tenant->isOnTrial()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Hesabınız askıya alınmıştır.',
                    ], Response::HTTP_FORBIDDEN);
                }

                abort(Response::HTTP_FORBIDDEN, 'Hesabınız askıya alınmıştır.');
            }
        }

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $this->tenantContext->clear();
    }
}
