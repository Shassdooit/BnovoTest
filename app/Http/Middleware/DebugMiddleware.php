<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $memoryStart = memory_get_usage();

        $response = $next($request);

        $end = microtime(true);
        $memoryEnd = memory_get_usage();

        $response->headers->set('X-Debug-Time', ($end - $start) * 1000);
        $response->headers->set('X-Debug-Memory', ($memoryEnd - $memoryStart) / 1024);

        return $response;
    }
}
