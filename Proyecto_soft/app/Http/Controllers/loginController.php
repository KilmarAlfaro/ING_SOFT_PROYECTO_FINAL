<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Paciente;
use App\Models\Doctor;

class LoginController extends Controller
{
    // Login Paciente (usa tabla pacientes)
    public function loginPac(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $paciente = Paciente::where('correo', $request->email)->first();
        if (! $paciente) {
            return back()->with('email_no_registrado', true);
        }

        if (! Hash::check($request->password, $paciente->password_hash)) {
            return back()->with('password_incorrecta', true);
        }

        // Guardar sesión
        Session::put('paciente_id', $paciente->id);
        Session::put('paciente_nombre', $paciente->nombre);

        return redirect()->route('mainPac');
    }

    // Login Doctor (usa tabla doctors)
    public function loginDoc(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $doctor = Doctor::where('correo', $request->email)->first();
        if (! $doctor) {
            return back()->with('email_no_registrado', true);
        }

        if (! Hash::check($request->password, $doctor->password_hash)) {
            return back()->with('password_incorrecta', true);
        }

        // Guardar sesión
        Session::put('doctor_id', $doctor->id);
        Session::put('doctor_nombre', $doctor->nombre);

        return redirect()->route('mainDoc');
    }
}
