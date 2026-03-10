<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $userPosition = 0;
        if ($userType === 'employee') {
            $userPosition = (int) optional(optional($user->employeeRS)->empPosition)->id;
        }

        abort_if($user->status != 1, 401, 'Your account is deactivated. Please contact the admin.');
        return response()->json([
            'message' => 'Login successful!',
            'user' => $userType,
            'position' => (int) $userPosition,
            'must_change_password' => (bool) $user->must_change_password,
            'csrfToken' => csrf_token(),
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
        $requiredLevelString = 'supervisor';

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

    public function forceUpdatePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        Validator::make($request->all(), [
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                'confirmed',
            ],
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($request->input('new_password')),
            'must_change_password' => false,
        ])->save();

        return response()->json([
            'message' => 'Password updated successfully.',
            'csrfToken' => csrf_token(),
        ]);
    }
}
