<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 *
 * API endpoints untuk autentikasi user.
 */
class AuthController extends Controller
{
    /**
     * Register user baru
     *
     * @bodyParam name string required Nama user. Example: John Doe
     * @bodyParam email string required Email. Example: john@example.com
     * @bodyParam password string required Password min 8 karakter. Example: password123
     * @bodyParam password_confirmation string required Konfirmasi password. Example: password123
     * @bodyParam role string required Role user (admin/user/supplier). Example: user
     *
     * @unauthenticated
     *
     * @response 201 {
     *   "success": true,
     *   "message": "User registered successfully.",
     *   "user": {"id": 1, "name": "John Doe", "email": "john@example.com", "role": "user"}
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 201)->cookie(
            'auth_token',
            $token,
            60,       // 60 menit
            '/',
            null,
            false,    // false untuk local development (true untuk production HTTPS)
            true,     // httpOnly
            false,
            'strict'
        );
    }

    /**
     * Login user
     *
     * @bodyParam email string required Email user. Example: john@example.com
     * @bodyParam password string required Password. Example: password123
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful.",
     *   "user": {"id": 1, "name": "John Doe", "email": "john@example.com", "role": "user"}
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Email atau password salah."
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ])->cookie(
            'auth_token',
            $token,
            60,       // 60 menit
            '/',
            null,
            false,    // false untuk local development
            true,     // httpOnly
            false,
            'strict'
        );
    }

    /**
     * Logout user
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logout successful."
     * }
     */
    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
        ])->cookie('auth_token', '', -1);
    }

    /**
     * Data user yang sedang login
     *
     * @response 200 {
     *   "success": true,
     *   "user": {"id": 1, "name": "John Doe", "email": "john@example.com", "role": "user"}
     * }
     */
    public function me(): JsonResponse
    {
        $user = Auth::user()->load('supplier');

        return response()->json([
            'success' => true,
            'user'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $user->role,
                'supplier_id' => $user->supplier?->id,
            ],
        ]);
    }
}