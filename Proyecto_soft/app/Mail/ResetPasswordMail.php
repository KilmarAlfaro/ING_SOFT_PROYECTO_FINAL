<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $resetUrl)
    {
    }

    public function build(): self
    {
        return $this->subject('Restablece tu contraseña - MedTech HUB')
            ->markdown('emails.reset-password', [
                'resetUrl' => $this->resetUrl,
            ]);
    }
}
