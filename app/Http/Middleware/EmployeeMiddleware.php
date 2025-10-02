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
        if (Auth::check()) {
            /** @var \App\Models\User */
            $user = Auth::user();
            if (!$user->user_type == 'employee') {
                Session::flash('error', 'Unauthorized: No employee data');
                return redirect()->back();
            }

            $departmentId = $user->employeeRS?->department;

            $requiredPrefix = match ($departmentId) {
                1 => '/',
                2 => 'humanresources',
                3 => 'finance',
                4 => 'procurement',
                default => null,
            };

            $currentPath = trim($request->path(), '/');
            
            if ($requiredPrefix && $requiredPrefix !== '/' && !str_starts_with($currentPath, $requiredPrefix)) {
                return redirect()->to('/' . $requiredPrefix);
            }
            return $next($request);
        }
        abort(401);
    }
}
