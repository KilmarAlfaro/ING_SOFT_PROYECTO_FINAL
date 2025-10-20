<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Support\Facades\Session;

class ConsultaController extends Controller
{
    // Paciente crea una consulta/mensaje para un doctor
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'mensaje' => 'required|string|max:2000',
        ]);

        // obtener paciente actual por sesión
        $pacienteId = Session::get('paciente_id');
        if (! $pacienteId) {
            return redirect()->route('loginPac');
        }

        $consulta = Consulta::create([
            'doctor_id' => $request->doctor_id,
            'paciente_id' => $pacienteId,
            'mensaje' => $request->mensaje,
            'status' => 'nuevo',
        ]);

        return back()->with('success', 'Mensaje enviado al doctor');
    }

    // Doctor ve sus consultas entrantes
    public function doctorIndex()
    {
        $doctorId = Session::get('doctor_id');
        if (! $doctorId) {
            return redirect()->route('loginDoc');
        }

        $consultas = Consulta::where('doctor_id', $doctorId)->orderBy('created_at', 'desc')->get();
        return view('consultas.doctor_index', compact('consultas'));
    }

    // Paciente ve sus consultas
    public function pacienteIndex()
    {
        $pacienteId = Session::get('paciente_id');
        if (! $pacienteId) {
            return redirect()->route('loginPac');
        }

        $consultas = Consulta::where('paciente_id', $pacienteId)->orderBy('created_at', 'desc')->get();
        return view('consultas.paciente_index', compact('consultas'));
    }

    // Doctor responde a una consulta
    public function responder(Request $request, Consulta $consulta)
    {
        $doctorId = Session::get('doctor_id');
        if (! $doctorId || $consulta->doctor_id != $doctorId) {
            abort(403);
        }

        $request->validate([
            'respuesta' => 'required|string|max:2000',
        ]);

        $consulta->respuesta = $request->respuesta;
        $consulta->status = 'respondido';
        $consulta->save();

        return back()->with('success', 'Respuesta enviada');
    }
}
