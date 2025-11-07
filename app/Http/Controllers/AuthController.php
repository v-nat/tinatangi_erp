<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ])->validate();

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed')
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();
        $userType = $user->user_type;
        $userPosition = $user->employeeRS->empPosition->id;

        abort_if($user->status != 1, 401, 'Your account is deactivated. Please contact the admin.');
        return response()->json([
            'message' => 'Login successful!',
            'user' => $userType,
            'position' => (int) $userPosition,
        ], 200);
    }

    public static function checkAuthorization()
    {
        if (!Auth::check()) {
            return false;
        }

        $hierarchy = [
            'ceo' => 4,
            'manager' => 3,
            'supervisor' => 2,
            'staff' => 1,
        ];

        $userLevelString = strtolower(Auth::user()->employeeRS->empPosition->level);
        $requiredLevelString = 'manager';

        if (!array_key_exists($userLevelString, $hierarchy)) {
            $userLevelNumber = 0;
        } else {
            $userLevelNumber = $hierarchy[$userLevelString];
        }

        $requiredLevelNumber = $hierarchy[$requiredLevelString];

        if ($userLevelNumber >= $requiredLevelNumber) {
            return true;
        }

        return false;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
