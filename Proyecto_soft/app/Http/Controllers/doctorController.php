<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    // LISTAR DOCTORES
    public function index()
    {
        $doctores = Doctor::all();
        return view('doctores.index', compact('doctores'));
    }

    // FORMULARIO DE REGISTRO
    public function create()
    {
        return view('registroDoc');
    }

    // GUARDAR DOCTOR
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email|unique:doctors,correo',
            'telefono' => 'required',
            'especialidad' => 'required',
            'numero_colegiado' => 'required',
            'usuario' => 'required|unique:doctors,usuario',
            'password' => 'required',
            'direccion_clinica' => 'required',
            'estado' => 'required',
        ]);

        $validated['password_hash'] = Hash::make($validated['password']);
        $validated['fecha_creacion'] = now();
        $validated['ultimo_login'] = null;

        unset($validated['password']);

        $doctor = Doctor::create($validated);

        return redirect()->route('doctores.create', $doctor)->with('success', 'Doctor registrado correctamente');
    }

    // MOSTRAR PERFIL (por id)
    public function show(Doctor $doctor)
    {
        return view('doctores.show', compact('doctor'));
    }

    // FORMULARIO DE EDICIÓN (por id)
    public function edit(Doctor $doctor)
    {
        return view('doctores.edit', compact('doctor'));
    }

    // ACTUALIZAR DOCTOR (por id)
    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email|unique:doctors,correo,' . $doctor->id,
            'telefono' => 'required',
            'especialidad' => 'required',
            'numero_colegiado' => 'required',
            'usuario' => 'required|unique:doctors,usuario,' . $doctor->id,
            'direccion_clinica' => 'required',
            'estado' => 'required',
        ]);

        $doctor->update($validated);

        return redirect()->route('doctores.show', $doctor)->with('success', 'Doctor actualizado correctamente');
    }

    // ELIMINAR DOCTOR
    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctores.index')->with('success', 'Doctor eliminado correctamente');
    }

    // ==============================
    // PERFIL DEL DOCTOR AUTENTICADO
    // ==============================

    public function showProfile()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return redirect()->back()->with('error', 'No se encontró la información del doctor.');
        }

        return view('perfilDoc', compact('doctor'));
    }

    public function updateProfile(Request $request)
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            return redirect()->back()->with('error', 'No se encontró el registro del doctor.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'especialidad' => 'required|string|max:255',
            'numero_colegiado' => 'required|string|max:100',
            'direccion_clinica' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'usuario' => 'required|string|max:100',
        ]);

        $doctor->update($validated);

        return redirect()->route('perfil.doctor')->with('success', 'Datos actualizados correctamente.');
    }
    // PERFIL INDIVIDUAL (sin autenticación)
        public function perfil($id)
            {
             $doctor = Doctor::find($id);

                if (!$doctor) {
                 return redirect()->route('doctores.index')->with('error', 'Doctor no encontrado.');
    }

            return view('perfilDoc', compact('doctor'));
            }

}
