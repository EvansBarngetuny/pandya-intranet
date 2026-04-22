<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has the required role
        if ($role === 'admin' && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }
        
        if ($role === 'hod' && !$user->isHOD() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. HOD privileges required.');
        }
        
        if ($role === 'staff' && !$user->isStaff() && !$user->isHOD() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. Staff privileges required.');
        }

        return $next($request);
    }
}