<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
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

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|max:4096',
        ]);

        $pacienteId = Session::get('paciente_id');
        $paciente = Paciente::find($pacienteId);

        if (! $paciente) {
            return redirect()->route('mainPac')->with('error', 'Paciente no encontrado.');
        }

        $paciente->nombre = $request->input('nombre');
        $paciente->descripcion = $request->input('descripcion');

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