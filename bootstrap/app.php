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
        $middleware->validateCsrfTokens(except: [
            'api/chat',
            'api/lead',
            'api/track',
        ]);
        $middleware->alias([
            'verify.api.token' => \App\Http\Middleware\VerifyApiToken::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\RedirectFirstPage::class,
        ]);
        // outermost, so it also sees the redirects answered by HandleDbRedirects (staging: links/redirects follow APP_URL)
        $middleware->prepend(\App\Http\Middleware\RewriteProductionUrls::class);
        $middleware->append(\App\Http\Middleware\HandleDbRedirects::class);
        $middleware->append(\App\Http\Middleware\StagingNoindex::class);
        $middleware->web(append: [\App\Http\Middleware\AttachLiteSpeedPurge::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
