<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;
    public $expiresIn;
    public $purpose;

    public function __construct($otp, $userName = null, $expiresIn = 10, $purpose = 'verification')
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->expiresIn = $expiresIn;
        $this->purpose = $purpose;
    }



    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}