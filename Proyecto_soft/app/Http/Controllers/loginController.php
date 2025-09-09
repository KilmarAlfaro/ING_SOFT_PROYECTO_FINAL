<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // o el modelo que uses para Paciente/Doctor

class LoginController extends Controller
{
    // Login Paciente
    public function loginPac(Request $request)
    {
        $user = User::where('email', $request->email)->where('role','paciente')->first();

        if (!$user) {
            return back()->with('email_no_registrado', true);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('password_incorrecta', true);
        }

        // Autenticación exitosa
        auth()->login($user);
        return redirect()->route('inicio');
    }

    // Login Doctor
    public function loginDoc(Request $request)
    {
        $user = User::where('email', $request->email)->where('role','doctor')->first();

        if (!$user) {
            return back()->with('email_no_registrado', true);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('password_incorrecta', true);
        }

        // Autenticación exitosa
        auth()->login($user);
        return redirect()->route('inicio');
    }
}
