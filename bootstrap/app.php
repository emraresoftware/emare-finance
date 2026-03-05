<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Authenticated rotalar için tenant resolve et
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\ResolveTenant::class,
        ]);

        $middleware->appendToGroup('api', [
            \App\Http\Middleware\ResolveTenant::class,
        ]);

        // Alias tanımları
        $middleware->alias([
            'module'      => \App\Http\Middleware\CheckModule::class,
            'permission'  => \App\Http\Middleware\CheckPermission::class,
            'tenant'      => \App\Http\Middleware\ResolveTenant::class,
            'super_admin' => \App\Http\Middleware\SuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
