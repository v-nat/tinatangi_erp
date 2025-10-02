<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    //
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
        $user_type = $user->user_type;
        abort_if($user->status != 1, 401, 'Your account is deactivated. Please contact the admin.');
        return response()->json([
            'message' => 'Login successful!',
            'user'=> $user_type,
        ], 200);
        
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Return a redirect response instead of JSON
        return redirect('/login');
    }
}
