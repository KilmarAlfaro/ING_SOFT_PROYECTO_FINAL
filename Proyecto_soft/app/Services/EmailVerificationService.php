<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EmailVerificationService
{
    public function send(User $user): void
    {
        $token = Str::random(64);

        EmailVerificationToken::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $token, 'expires_at' => now()->addDay()]
        );

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addDay(),
            ['token' => $token]
        );

        Mail::to($user->email)->send(new VerifyEmail($user, $url));
    }
}
