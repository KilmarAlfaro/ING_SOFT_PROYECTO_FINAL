<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;

class PerfilDoctorController extends Controller
{
    // Mostrar el perfil del doctor autenticado
    public function show()
    {
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        return view('perfilDoc', compact('doctor'));
    }

    // Actualizar los datos del perfil
    public function update(Request $request)
    {
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'especialidad' => 'required|string|max:100',
            'numero_colegiado' => 'required|string|max:50',
            'direccion_clinica' => 'required|string|max:255',
            'correo' => 'required|email',
            'usuario' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        // Actualiza los datos personales
        $doctor->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'especialidad' => $request->especialidad,
            'numero_colegiado' => $request->numero_colegiado,
            'direccion_clinica' => $request->direccion_clinica,
        ]);

        // Actualiza datos de usuario (tabla users)
        $user = Auth::user();
        $user->name = $request->usuario;
        $user->email = $request->correo;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return redirect()->route('perfil.doctor')->with('success', 'Perfil actualizado correctamente.');
    }
}

