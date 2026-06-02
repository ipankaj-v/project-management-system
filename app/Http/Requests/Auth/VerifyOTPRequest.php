<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class VerifyOTPRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6'
        ];
    }


    public function messages(): array
    {
        return [
            // Email field messages
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.exists' => 'This email address is not registered in our system.',

            // OTP field messages
            'otp.required' => 'OTP code is required.',
            'otp.digits' => 'OTP code must be exactly 6 digits.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
