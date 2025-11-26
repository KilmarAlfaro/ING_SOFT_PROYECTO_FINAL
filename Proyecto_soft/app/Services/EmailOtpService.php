<?php

namespace App\Services;

use App\Mail\VerifyEmailOtp;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailOtpService
{
    private const EXPIRATION_MINUTES = 10;

    public function send(string $email): void
    {
        $normalizedEmail = strtolower(trim($email));
        $code = (string) random_int(1000, 9999);

        EmailVerificationCode::updateOrCreate(
            ['email' => $normalizedEmail],
            [
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
                'attempts' => 0,
            ]
        );

        Mail::to($normalizedEmail)->send(new VerifyEmailOtp($code));
    }

    public function isValid(string $email, string $code): bool
    {
        $record = EmailVerificationCode::where('email', strtolower(trim($email)))->first();
        if (!$record || $record->expires_at->isPast()) {
            return false;
        }

        return Hash::check($code, $record->code);
    }

    public function consume(string $email): void
    {
        EmailVerificationCode::where('email', strtolower(trim($email)))->delete();
    }
}
