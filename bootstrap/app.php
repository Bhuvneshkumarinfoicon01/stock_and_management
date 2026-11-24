<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleRedirect;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Run first so session cookie path and URLs work when app is in a subdirectory (e.g. localhost/stockmgnt/public)
        $middleware->web(prepend: [App\Http\Middleware\SubdirectorySession::class]);
        $middleware->append(App\Http\Middleware\RoleRedirect::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
