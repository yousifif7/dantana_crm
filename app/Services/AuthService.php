<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load(['role', 'department']),
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['employee_id'] = $this->generateEmployeeId();
        
        return User::create($data);
    }

    private function generateEmployeeId(): string
    {
        $lastUser = User::latest('id')->first();
        $lastId = $lastUser ? intval(substr($lastUser->employee_id, 3)) : 0;
        
        return 'EMP' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }

    public function enableTwoFactor(User $user, string $secret): void
    {
        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true,
        ]);
    }

    public function disableTwoFactor(User $user): void
    {
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);
    }

    public function verifyTwoFactor(User $user, string $code): bool
    {
        // Implement TOTP verification here
        // This would use a package like pragmarx/google2fa
        return true;
    }
}

