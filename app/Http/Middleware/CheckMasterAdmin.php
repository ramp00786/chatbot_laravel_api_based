<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMasterAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has the admin email
        if (!auth()->check() || auth()->user()->email !== 'pratibhasahu9713@gmail.com') {
            abort(403, 'Unauthorized - Admin access required');
        }

        return $next($request);
    }
}
