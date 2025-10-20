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

        // Si existe un usuario en la tabla users con este correo, autenticar con Laravel Auth
        $user = User::where('email', $request->email)->where('role', 'paciente')->first();
        if ($user) {
            Auth::login($user);
        } else {
            // Fallback a la sesi3n antigua
            Session::put('paciente_id', $paciente->id);
            Session::put('paciente_nombre', $paciente->nombre);
            Session::put('paciente_sexo', $paciente->sexo ?? null);
        }

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

        // Si existe un usuario en la tabla users con este correo, autenticar con Laravel Auth
        $user = User::where('email', $request->email)->where('role', 'doctor')->first();
        if ($user) {
            Auth::login($user);
        } else {
            // Fallback a la sesi3n antigua
            Session::put('doctor_id', $doctor->id);
            Session::put('doctor_nombre', $doctor->nombre);
            Session::put('doctor_sexo', $doctor->sexo ?? null);
        }

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
            'apellido' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'numero_dui' => 'required|string|max:20',
            'sexo' => 'nullable|string|in:Masculino,Femenino',
            'correo' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->nombre . ($request->apellido ? ' ' . $request->apellido : ''),
            'telefono' => $request->telefono,
            'dui' => $request->numero_dui,
            'direccion' => $request->direccion ?? null,
            'email' => $request->correo,
            'password' => Hash::make($request->password),
            'role' => 'paciente',
        ]);

        Auth::login($user);

        // Optionally create a Paciente record so legacy session flows can use it later
        $paciente = null;
        try {
            $paciente = \App\Models\Paciente::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido ?? '',
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'numero_dui' => $request->numero_dui ?? null,
                'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
                'sexo' => $request->sexo ?? null,
                'password_hash' => Hash::make($request->password),
            ]);
        } catch (\Exception $e) {
            // ignore failures creating paciente record; user account exists and can proceed
        }

        // Populate legacy session keys to support views that rely on them
        if ($paciente) {
            Session::put('paciente_id', $paciente->id);
            Session::put('paciente_nombre', $paciente->nombre);
            Session::put('paciente_sexo', $paciente->sexo ?? null);
        } else {
            // fallback to the Auth user name so UI still has a name
            Session::put('paciente_nombre', $user->name);
            Session::put('paciente_sexo', $request->sexo ?? null);
        }

        return redirect()->route('mainPac');
    }

    /* ==============================
       REGISTRO DOCTOR
       ============================== */
    public function registroDoc(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'numero_dui' => 'required|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|string|in:Masculino,Femenino',
            'especialidad' => 'required|string|max:255',
            'especialidad_otro' => 'nullable|string|max:255',
            'direccion_clinica' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->nombre . ($request->apellido ? ' ' . $request->apellido : ''),
            'telefono' => $request->telefono,
            'dui' => $request->numero_dui,
            'direccion' => $request->direccion_clinica ?? null,
            'email' => $request->correo,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
        ]);

        Auth::login($user);

        // Crear registro en tabla doctors y vincular con el usuario creado.
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido ?? '',
            'correo' => $request->correo,
            'telefono' => $request->telefono ?? '',
            'especialidad' => ($request->especialidad == 'Otro' && $request->especialidad_otro) ? $request->especialidad_otro : ($request->especialidad ?? 'General'),
            'numero_colegiado' => $request->numero_colegiado ?? 'N/A',
            'password_hash' => Hash::make($request->password),
            'direccion_clinica' => $request->direccion_clinica ?? '',
            'estado' => 'activo',
            'sexo' => $request->sexo ?? null,
            'numero_dui' => $request->numero_dui ?? null,
            'fecha_nacimiento' => $request->fecha_nacimiento ?? null,
            'descripcion' => $request->descripcion ?? null,
        ]);

        // Populate legacy session keys for UI that relies on them
        if ($doctor) {
            Session::put('doctor_id', $doctor->id);
            Session::put('doctor_nombre', $doctor->nombre);
            Session::put('doctor_sexo', $doctor->sexo ?? null);
        } else {
            Session::put('doctor_nombre', $user->name);
            Session::put('doctor_sexo', $request->sexo ?? null);
        }

        return redirect()->route('mainDoc');
    }
}

