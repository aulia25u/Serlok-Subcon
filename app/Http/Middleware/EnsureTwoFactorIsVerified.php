<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $request->session()->get('two_factor_passed')) {
            return $next($request);
        }

        if ($request->routeIs('two-factor.challenge') ||
            $request->routeIs('two-factor.verify') ||
            $request->routeIs('two-factor.skip') ||
            $request->routeIs('logout') ||
            $request->routeIs('profile.*')) {
            return $next($request);
        }

        return redirect()->route('two-factor.challenge');
    }
}
