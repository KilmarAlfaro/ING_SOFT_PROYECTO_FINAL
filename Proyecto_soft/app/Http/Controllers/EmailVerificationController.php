<?php

namespace App\Http\Controllers;

use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Services\EmailOtpService;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(
        private readonly EmailVerificationService $service,
        private readonly EmailOtpService $otpService,
    )
    {
    }

    public function notice(Request $request)
    {
        $email = $request->session()->get('verification_email');
        $role = $request->session()->get('verification_role');

        if (! $email) {
            return redirect()->route('inicio');
        }

        return view('auth.verification-notice', compact('email', 'role'));
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
        }

        if ($user->email_verified_at) {
            return back()->with('status', 'Ese correo ya está verificado.');
        }

        $this->service->send($user);

        return back()->with('status', 'Se envió un nuevo enlace de verificación.');
    }

    public function verify(string $token, Request $request)
    {
        $record = EmailVerificationToken::where('token', $token)->first();
        if (! $record || $record->expires_at->isPast()) {
            return redirect()->route('inicio')->with('error', 'El enlace de verificación expiró. Solicita uno nuevo.');
        }

        $user = $record->user;
        if (! $user) {
            return redirect()->route('inicio')->with('error', 'No pudimos encontrar al usuario.');
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $record->delete();

        $loginRoute = $user->role === 'doctor' ? 'loginDoc' : 'loginPac';

        return redirect()->route($loginRoute)->with('status', 'Correo verificado correctamente. Ahora puedes iniciar sesión.');
    }

    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $this->otpService->send($data['email']);

        return response()->json(['message' => 'Código enviado.']);
    }

    public function validateOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:4',
        ]);

        if (! $this->otpService->isValid($data['email'], $data['code'])) {
            return response()->json(['message' => 'El código es incorrecto o expiró.'], 422);
        }

        return response()->json(['message' => 'Código válido.']);
    }

}
