<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Paciente;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginRegistroController extends Controller
{
    /* ==============================
       LOGIN PACIENTE
       ============================== */
    public function loginPac(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar paciente por correo
        $paciente = Paciente::where('correo', $request->email)->first();

        if (!$paciente) {
            return back()->with('email_no_registrado', true);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $paciente->password_hash)) {
            return back()->with('password_incorrecta', true);
        }

        // Guardar sesión
        Session::put('paciente_id', $paciente->id);
        Session::put('paciente_nombre', $paciente->nombre);

        // Redirigir a dashboard
        return redirect()->route('mainPac');
    }

    /* ==============================
       LOGIN DOCTOR
       ============================== */
    public function loginDoc(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar doctor por correo
        $doctor = Doctor::where('correo', $request->email)->first();

        if (!$doctor) {
            return back()->with('email_no_registrado', true);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $doctor->password_hash)) {
            return back()->with('password_incorrecta', true);
        }

        // Guardar sesión
        Session::put('doctor_id', $doctor->id);
        Session::put('doctor_nombre', $doctor->nombre);

        // Redirigir al dashboard del doctor
        return redirect()->route('mainDoc');
    }

    /* ==============================
       REGISTRO PACIENTE
       ============================== */
    public function registroPac(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'dui' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->nombre,
            'telefono' => $request->telefono,
            'dui' => $request->dui,
            'direccion' => $request->direccion,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'paciente',
        ]);

        Auth::login($user);

        return redirect()->route('mainPac');
    }

    /* ==============================
       REGISTRO DOCTOR
       ============================== */
    public function registroDoc(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'dui' => 'required|string|max:20',
            'especialidad' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->nombre,
            'telefono' => $request->telefono,
            'dui' => $request->dui,
            'especialidad' => $request->especialidad,
            'direccion' => $request->direccion,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
        ]);

        Auth::login($user);

        return redirect()->route('mainDoc');
    }
}

