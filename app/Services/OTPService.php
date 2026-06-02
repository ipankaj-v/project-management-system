<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class OTPService
{
    public function generateOTP(): array
    {
        return [
            'otp' => rand(100000, 999999),
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ];
    }

    public function verifyOTP(string $email, string $otp): ?User
    {
        return User::where('email', $email)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();
    }

    public function verifyOTPAndVerifyEmail(string $email, string $otp): ?User
    {
        $user = $this->verifyOTP($email, $otp);

        if ($user) {
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
                'email_verified_at' => Carbon::now()
            ]);
        }

        return $user;
    }

    public function clearOTP(User $user): void
    {
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);
    }
}
