<?php

namespace App\Jobs;

use App\Mail\OTPMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $email;
    protected $otp;


    public function __construct($name, $email, $otp)
    {
        $this->name = $name;
        $this->email = $email;
        $this->otp = $otp;
    }



    public function handle(): void
    {
        Mail::to($this->email)->send(new OTPMail(
            otp: $this->otp,
            userName: $this->name,
            expiresIn: 10,
            purpose: 'verification'
        ));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendMailJob failed: ' . $exception->getMessage());
        Log::error('Exception details: ', [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}