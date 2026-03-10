<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
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
            'attendance',
            'employee',
        ];

        if (Auth::check()) {
            /** @var \App\Models\User */
            $user = Auth::user();

            $departmentId = $user->employeeRS?->department;

            if (!$user->user_type == 'employee' || !$departmentId) {
                Session::flash('error', 'Unauthorized: No employee data');
                abort(401);
            }

            $currentPath = trim($request->path(), '/');

            foreach ($excludedRoutes as $excludedRoute) {
                if ($currentPath === $excludedRoute || str_starts_with($currentPath, $excludedRoute . '/')) {
                    return $next($request);
                }
            }
            $requiredPrefix = match ((int) $departmentId) {
                0 => '/',
                1 => '/',
                2 => 'human-resources',
                3 => 'finance',
                4 => 'procurement',
                5 => 'inventory',
                6 => 'customer-service',
                7 => 'operations',
                8 => 'operations',
                9 => 'operations',
                default => null,
            };

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
