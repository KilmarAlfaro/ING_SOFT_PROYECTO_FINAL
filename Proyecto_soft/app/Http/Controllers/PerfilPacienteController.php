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

        $request->validate([
            'nombre' => 'nullable|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'nullable|email|unique:pacientes,correo,' . ($paciente ? $paciente->id : 'NULL'),
            'password' => 'nullable|string|min:6|confirmed',
            'descripcion' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|max:4096',
        ]);

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
            // eliminar foto anterior si existe
            if ($paciente->foto_perfil && Storage::disk('public')->exists('profile_pics/' . $paciente->foto_perfil)) {
                Storage::disk('public')->delete('profile_pics/' . $paciente->foto_perfil);
            }

            $path = $request->file('profile_image')->store('profile_pics', 'public');
            $paciente->foto_perfil = basename($path);
        }

        $paciente->save();

        return redirect()->route('perfil.paciente')->with('success', 'Perfil actualizado correctamente.');
    }
}