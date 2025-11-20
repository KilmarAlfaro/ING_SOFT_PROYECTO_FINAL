<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Paciente;

class PerfilPacienteController extends Controller
{
    public function edit()
    {
        // Verificar sesión personalizada (loginController usa 'paciente_id')
        if (! Session::has('paciente_id')) {
            return redirect()->route('loginPac')->with('error', 'Debes iniciar sesión como paciente.');
        }

        $pacienteId = Session::get('paciente_id');
        $paciente = Paciente::find($pacienteId);

        if (! $paciente) {
            // Si por alguna razón no existe, volver al main paciente
            return redirect()->route('mainPac')->with('error', 'Paciente no encontrado en la base de datos.');
        }

        return view('perfilPac', compact('paciente'));
    }

    public function update(Request $request)
    {
        if (! Session::has('paciente_id')) {
            return redirect()->route('loginPac')->with('error', 'Debes iniciar sesión como paciente.');
        }

        // recuperar paciente para usar su id en la regla unique del correo
        $pacienteId = Session::get('paciente_id');
        $paciente = Paciente::find($pacienteId);

        $messages = [
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];

        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'nullable|email|unique:pacientes,correo,' . ($paciente ? $paciente->id : 'NULL'),
            'password' => 'nullable|string|min:6|confirmed',
            'descripcion' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|max:4096',
        ], $messages);

        if (! $paciente) {
            return redirect()->route('mainPac')->with('error', 'Paciente no encontrado.');
        }

        // Solo actualizar los campos que vienen rellenados (no vacíos)
        if ($request->filled('nombre')) {
            $paciente->nombre = $request->input('nombre');
        }
        if ($request->filled('apellido')) {
            $paciente->apellido = $request->input('apellido');
        }
        if ($request->filled('telefono')) {
            $paciente->telefono = $request->input('telefono');
        }
        if ($request->filled('direccion')) {
            $paciente->direccion = $request->input('direccion');
        }
        if ($request->filled('correo')) {
            $paciente->correo = $request->input('correo');
        }

        if ($request->filled('descripcion')) {
            $paciente->descripcion = $request->input('descripcion');
        }

        // contraseña: si viene, actualizar el hash
        if ($request->filled('password')) {
            $paciente->password_hash = Hash::make($request->input('password'));
        }

        if ($request->hasFile('profile_image')) {
            // IMPORTANTE: capturar bytes y mime ANTES de mover/almacenar el archivo
            $image = $request->file('profile_image');
            $bytes = @file_get_contents($image->getRealPath());
            $mime = $image->getMimeType() ?: 'image/jpeg';

            // Guardar en disco configurado (opcional)
            $disk = config('avatar.disk', 'public');
            $folder = config('avatar.folder', 'profile_pics');
            $path = $image->store($folder, $disk);
            $paciente->foto_perfil = basename($path);

            // Guardar bytes en BD para portabilidad (usar los bytes capturados)
            if ($bytes !== false && strlen($bytes) > 0) {
                $paciente->foto_perfil_blob = $bytes;
                $paciente->foto_perfil_mime = $mime;
            }
        }

        $paciente->save();

        return redirect()->route('perfil.paciente')->with('success', 'Perfil actualizado correctamente.');
    }
}