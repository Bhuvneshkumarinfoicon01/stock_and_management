<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures session and URLs work when the app runs in a subdirectory
 * (e.g. http://localhost/stockmgnt/public/). Must run before StartSession.
 */
class SubdirectorySession
{
    public function handle(Request $request, Closure $next): Response
    {
        $basePath = $request->getBasePath();

        // Force app URL to current request root so form actions and redirects stay on same origin
        URL::forceRootUrl($request->root());

        // Session cookie path must match the app path so the browser sends the cookie on every request
        config(['session.path' => $basePath !== '' ? $basePath : '/']);

        return $next($request);
    }
}
