<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Permission Middleware
 *
 * Middleware for permission-based access control
 * More granular than role-based access control
 */
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user has the required permission using Gate
        if (!\Illuminate\Support\Facades\Gate::allows($permission)) {
            abort(403, 'Access denied. You do not have the required permission.');
        }

        return $next($request);
    }
}
