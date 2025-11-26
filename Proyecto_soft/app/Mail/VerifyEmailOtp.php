<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailOtp extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
    }

    public function build(): self
    {
        return $this->subject('Tu código de verificación de MedTech HUB')
            ->markdown('emails.verify-email-otp', [
                'code' => $this->code,
                'expirationMinutes' => 10,
            ]);
    }
}
