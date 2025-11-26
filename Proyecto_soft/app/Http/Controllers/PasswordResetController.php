<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function requestForm(Request $request)
    {
        $role = $request->query('role');
        return view('auth.passwords.email', compact('role'));
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $url = route('password.reset', ['token' => $token, 'email' => $user->email]);
        Mail::to($user->email)->send(new ResetPasswordMail($url));

        return back()->with('status', 'Te enviamos un enlace de restablecimiento. Revisa tu correo.');
    }

    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');
        if (! $email) {
            return redirect()->route('password.request')->withErrors(['email' => 'El enlace es inválido.']);
        }

        return view('auth.passwords.reset', compact('token', 'email'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'El token no es válido o caducó.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'No encontramos la cuenta.']);
        }

        $hashed = Hash::make($request->password);
        $user->forceFill(['password' => $hashed])->save();

        if ($user->role === 'doctor') {
            Doctor::where('correo', $user->email)->update(['password_hash' => $hashed]);
        } elseif ($user->role === 'paciente') {
            Paciente::where('correo', $user->email)->update(['password_hash' => $hashed]);
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $loginRoute = $user->role === 'doctor' ? 'loginDoc' : 'loginPac';

        return redirect()->route($loginRoute)->with('status', 'Contraseña actualizada. Ahora puedes iniciar sesión.');
    }
}
