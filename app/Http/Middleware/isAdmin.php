<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     * middleware for role based permission allowing only admin to acess some endpoints
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role === "admin") {
            return $next($request);
        }
        return response()->json(['message' => 'Forbidden: Only admin can access this.'], 403);
    }
}
