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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\RedirectBasedOnRole::class,
            \App\Http\Middleware\TeamAccessMiddleware::class,
            \App\Http\Middleware\TrackUserSession::class,
        ]);
        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuration des exceptions si nécessaire
    })
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        App\Providers\UserSessionServiceProvider::class,
    ])
    ->create();
