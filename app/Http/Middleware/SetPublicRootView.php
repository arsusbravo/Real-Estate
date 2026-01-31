<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SetPublicRootView
{
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::setRootView('public');

        return $next($request);
    }
}
