<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SupplierMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $excludedRoutes = [
            'profile',
            'settings',
        ];

        if (Auth::check()) {
            /** @var \App\Models\User */
            $user = Auth::user();
            $departmentId = $user->employeeRS?->department;
            if (!$user->user_type == 'supplier' || $departmentId) {
                abort(401);
            }
            $currentPath = trim($request->path(), '/');

            foreach ($excludedRoutes as $excludedRoute) {
                if ($currentPath === $excludedRoute || str_starts_with($currentPath, $excludedRoute . '/')) {
                    return $next($request);
                }
            }
            $requiredPrefix = 'supplier';

            if ($requiredPrefix && $requiredPrefix !== '/') {
                if (!str_starts_with($currentPath, $requiredPrefix)) {
                    return redirect()->to('/' . $requiredPrefix);
                }
            }
            return $next($request);
        }
        abort(401);
    }
}
