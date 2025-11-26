<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $verificationUrl)
    {
    }

    public function build(): self
    {
        return $this->subject('Confirma tu correo en MedTech HUB')
            ->markdown('emails.verify-email', [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}
