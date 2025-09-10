<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Paciente;

class pacienteController extends Controller
{
    public function index()
    {
        $paciente = Paciente::all();
        return view('mainPaciente', compact('paciente'));
    }

    public function create()
    {
        return view('registroPac');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:10',
            'correo' => 'required|email|unique:pacientes,correo',
            'telefono' => 'required|string|max:15',
            'direccion' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',   
        ]);

        Paciente::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'password_hash' => Hash::make($request->password),
            'fecha_creacion' => now(),
        ]);

        return redirect()->route('paciente.create')->with('success', 'Paciente registrado correctamente');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('editarPaciente', compact('paciente'));
    }

    // Actualizar paciente
    public function update(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:10',
            'correo' => 'required|email|unique:pacientes,correo,' . $paciente->id,
            'telefono' => 'required|string|max:15',
            'direccion' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',   
        ]);

        $paciente->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'password_hash' => $request->password ? Hash::make($request->password) : $paciente->password_hash,
        ]);
        return redirect()->route('paciente.index')->with('success', 'Paciente actualizado correctamente');
    }

    // Eliminar paciente
    public function destroy($id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->delete();

        return redirect()->route('paciente.index')->with('success', 'Paciente eliminado correctamente');
    }
}