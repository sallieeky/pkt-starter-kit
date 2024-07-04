<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  \Illuminate\Http\Request  $request
     * @param  string|int|array|Role|Collection|\BackedEnum  $roles
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if (! $request->user()?->hasRole($roles)) {
            abort(404);
        }

        return $next($request);
    }
}
