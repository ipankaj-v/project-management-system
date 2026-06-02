<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendOTPRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Jobs\SendMailJob;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{

    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }


    public function forgotPassword(SendOTPRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        // Generate OTP using service
        $otpData = $this->otpService->generateOTP();

        try {
            // Send OTP via Email
            SendMailJob::dispatch(
                $user->name,
                $user->email,
                $otpData['otp']
            );

            $user->update([
                'otp' => $otpData['otp'],
                'otp_expires_at' => $otpData['otp_expires_at']
            ]);

            return new SuccessResource([
                'message' => 'OTP sent successfully to your email',
                'data' => [
                    'expires_in' => 10,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            return new ErrorResource([
                'message' => 'Failed to send OTP. Please try again.',
                'error_code' => 'EMAIL_SEND_FAILED',
                'status_code' => 500
            ]);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

  // Use basic OTP verification for password reset (no email verification)
        $user = $this->otpService->verifyOTP(
            $validated['email'],
            $validated['otp']
        );


        if (!$user) {
            return new ErrorResource([
                'message' => 'Invalid or expired OTP',
                'status_code' => 400
            ]);
        }

        // Clear OTP and mark email as verified
        $this->otpService->clearOTP($user);

        $user->tokens()->delete();

        return new SuccessResource([
            'message' => 'Password reset successfully'
        ]);
    }
}