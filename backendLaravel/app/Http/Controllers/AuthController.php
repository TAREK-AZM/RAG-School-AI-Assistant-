<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Signup
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            "is_admin" => true
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;


        return response()
            ->json(['message' => 'Registered successfully', 'user' => $user], 201)
            ->withCookie(cookie(
                'auth_token',
                $token,
                config('sanctum.expiration'),
                '/',
                null,
                true,
                true,
                false,
                'Strict'
            ));
    }


    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['user' => $user])
            ->withCookie(cookie(
                'auth_token',
                $token,
                config('sanctum.expiration'),
                '/',
                null,
                false, // Set true in production (HTTPS)
                true,  // HttpOnly
                false,
                'Lax'
            ));
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()
            ->json(['message' => 'Successfully logged out'])
            ->withCookie(cookie()->forget('auth_token'));
    }

    // Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['email' => __($status)], 400);
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => __($status)])
            : response()->json(['email' => [__($status)]], 400);
    }

    // convert user to admin
    public function convertToAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request['email'])->firstOrFail();
        $user->is_admin = true;
        $user->save();

        return response()->json([
            'message' => 'User converted to admin successfully',
            'user' => $user
        ]);
    }

    public function getUser(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'user' => $user
        ], 200);
    }
}
