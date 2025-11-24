<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

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
            'descripcion' => 'nullable|string|max:1000',
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
        $doctor->descripcion = $request->descripcion;
    // 'usuario' field removed: keep DB consistent but do not update username here

        // Actualizar password solo si fue enviado
        if ($request->filled('password')) {
            $doctor->password_hash = bcrypt($request->password);
        }

        // manejar imagen de perfil
        if ($request->hasFile('profile_image')) {
            // Capturar bytes y mime ANTES de almacenar/mover el archivo
            $image = $request->file('profile_image');
            $bytes = @file_get_contents($image->getRealPath());
            $mime = $image->getMimeType() ?: 'image/jpeg';

            // Guardar en disco configurado (opcional, como respaldo)
            $disk = config('avatar.disk', 'public');
            $folder = config('avatar.folder', 'profile_pics');
            $path = $image->store($folder, $disk);
            // Guardar nombre sólo si la columna existe
            if (Schema::hasColumn('doctors', 'foto_perfil')) {
                // eliminar archivo anterior si existía
                if (! empty($doctor->foto_perfil) && Storage::disk($disk)->exists($folder.'/'.$doctor->foto_perfil)) {
                    try { Storage::disk($disk)->delete($folder.'/'.$doctor->foto_perfil); } catch (\Exception $e) { /* ignore */ }
                }
                $doctor->foto_perfil = basename($path);
            }

            // Guardar bytes en BD para portabilidad sólo si las columnas existen
            if ($bytes !== false && strlen($bytes) > 0) {
                if (Schema::hasColumn('doctors', 'foto_perfil_blob')) {
                    $doctor->foto_perfil_blob = $bytes;
                }
                if (Schema::hasColumn('doctors', 'foto_perfil_mime')) {
                    $doctor->foto_perfil_mime = $mime;
                }
            }
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

