<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:doctors,correo',
            'telefono' => 'required|string|max:20',
            'especialidad' => 'required|string|max:100',
            'numero_colegiado' => 'required|string|max:50',
            // removed 'usuario' from registration - login via correo + password
            'password' => 'required|string|min:6|confirmed',
            'direccion_clinica' => 'required|string|max:255',
            // additional fields
            'sexo' => 'nullable|string|in:Masculino,Femenino',
            'numero_dui' => 'nullable|string|max:32',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        $data = $validated;
        $data['password_hash'] = Hash::make($validated['password']);
    $data['fecha_creacion'] = now();
        $data['ultimo_login'] = null;
        // default estado to activo on registration
        $data['estado'] = 'activo';
        unset($data['password']);
        unset($data['password_confirmation']);

        $doctor = Doctor::create($data);

        // set legacy session like login
        session()->put('doctor_id', $doctor->id);
        session()->put('doctor_nombre', $doctor->nombre);

        return redirect()->route('mainDoc')->with('success', 'Se ha registrado correctamente');
    }

    // MOSTRAR PERFIL (por id)
    public function show(Doctor $doctor)
    {
        $doctor = \App\Models\Doctor::where('user_id', \Auth::id())->firstOrFail();
        return view('perfilDoc', compact('doctor'));
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
               // 'usuario' removed: login by correo
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

    // BUSCADOR PARA DOCTORES DOCTOR
    public function buscar(Request $request)
    {
        $q = $request->input('q');
        $especialidad = $request->input('especialidad');

        $query = Doctor::query();
        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('nombre', 'LIKE', "%$q%")
                    ->orWhere('apellido', 'LIKE', "%$q%");
            });
        }
        if ($especialidad) {
            $query->where('especialidad', 'LIKE', "%$especialidad%");
        }

        // If this is an AJAX/JSON request return a compact JSON list (used for live search)
        if ($request->wantsJson() || $request->ajax()) {
            $items = $query->orderBy('nombre')->limit(12)->get()->map(function($d){
                $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                $foto = route('avatar.doctor', $d->id);
                return [
                    'id' => $d->id,
                    'nombre' => $d->nombre,
                    'apellido' => $d->apellido,
                    'especialidad' => $d->especialidad,
                    'descripcion' => $d->descripcion,
                    'foto' => $foto,
                ];
            });

            return response()->json(['data' => $items]);
        }

        $doctores = $query->orderBy('nombre')->paginate(12)->withQueryString();

        return view('buscar.resultados', compact('doctores', 'q', 'especialidad'));
    }

    // GESTIONAR CONSULTA
        public function consulta($id)
    {
        $doctor = Doctor::findOrFail($id);
        // Aquí puedes manejar la lógica para realizar la consulta
            return view('consulta', compact('doctor'));
    }
}