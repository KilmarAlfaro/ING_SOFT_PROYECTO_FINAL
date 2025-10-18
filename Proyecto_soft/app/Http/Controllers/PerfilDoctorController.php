<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;

class PerfilDoctorController extends Controller
{
    // No middleware here: we accept both Auth users and legacy Session-based logins.

    // Mostrar el perfil del doctor autenticado
    public function show()
    {
        // Soportar dos flujos: usuario autenticado (users -> doctors.user_id) o sesión antigua (Session doctor_id)
        if (Auth::check()) {
            $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        } elseif (Session::has('doctor_id')) {
            $doctor = Doctor::findOrFail(Session::get('doctor_id'));
        } else {
            // No autenticado
            abort(403);
        }

        return view('perfilDoc', compact('doctor'));
    }

    // Actualizar los datos del perfil
    public function update(Request $request)
    {
        // Obtener doctor (soporta Auth o sesión legacy)
        if (Auth::check()) {
            $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        } elseif (Session::has('doctor_id')) {
            $doctor = Doctor::findOrFail(Session::get('doctor_id'));
        } else {
            abort(403);
        }

        $messages = [
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'especialidad' => 'required|string|max:100',
            'numero_colegiado' => 'required|string|max:50',
            'direccion_clinica' => 'required|string|max:255',
            'correo' => 'required|email',
            'password' => 'nullable|string|min:6|confirmed',
            'profile_image' => 'nullable|image|max:4096',
        ], $messages);

        // Actualiza los datos personales en tabla doctors
        $doctor->nombre = $request->nombre;
        $doctor->apellido = $request->apellido;
        $doctor->telefono = $request->telefono;
        $doctor->especialidad = $request->especialidad;
        $doctor->numero_colegiado = $request->numero_colegiado;
        $doctor->direccion_clinica = $request->direccion_clinica;
        $doctor->correo = $request->correo;
    // 'usuario' field removed: keep DB consistent but do not update username here

        // Actualizar password solo si fue enviado
        if ($request->filled('password')) {
            $doctor->password_hash = bcrypt($request->password);
        }

        // manejar imagen de perfil
        if ($request->hasFile('profile_image')) {
            // eliminar anterior si existe
            if ($doctor->foto_perfil && Storage::disk('public')->exists('profile_pics/' . $doctor->foto_perfil)) {
                Storage::disk('public')->delete('profile_pics/' . $doctor->foto_perfil);
            }

            $path = $request->file('profile_image')->store('profile_pics', 'public');
            $doctor->foto_perfil = basename($path);
        }

        $doctor->save();

        // Si el doctor tiene un user asociado (Auth), actualizamos también tabla users
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                // Use the doctor's full name for the user name
                $user->name = $request->nombre . ' ' . $request->apellido;
                $user->email = $request->correo;
                if ($request->filled('password')) {
                    $user->password = bcrypt($request->password);
                }
                $user->save();
            }
        }

        return redirect()->route('perfil.doctor')->with('success', 'Perfil actualizado correctamente.');
    }

}

