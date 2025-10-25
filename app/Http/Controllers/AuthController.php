<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $result = $this->authService->login($request->email, $request->password);

        if (!$result) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json($result);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'hire_date' => 'required|date',
        ]);

        $user = $this->authService->register($validated);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->load(['role', 'department']),
        ], 201);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load(['role', 'department', 'supervisor']));
    }

    public function enableTwoFactor(Request $request)
    {
        $request->validate(['secret' => 'required|string']);
        
        $this->authService->enableTwoFactor($request->user(), $request->secret);
        
        return response()->json(['message' => '2FA enabled successfully']);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $valid = $this->authService->verifyTwoFactor($request->user(), $request->code);
        
        if ($valid) {
            session(['2fa_verified' => true]);
            return response()->json(['message' => '2FA verified successfully']);
        }
        
        return response()->json(['message' => 'Invalid code'], 400);
    }

    public function disableTwoFactor(Request $request)
    {
        $this->authService->disableTwoFactor($request->user());
        
        return response()->json(['message' => '2FA disabled successfully']);
    }
}