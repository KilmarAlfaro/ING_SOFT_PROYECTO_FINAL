<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\ConsultaMensaje;
use Illuminate\Support\Facades\Session;

class ConsultaController extends Controller
{
    // Paciente crea una consulta/mensaje para un doctor
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'motivo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:5000',
        ]);

        // obtener paciente actual por sesión
        $pacienteId = Session::get('paciente_id');
        if (! $pacienteId) {
            return redirect()->route('loginPac');
        }

        // Evitar duplicados: si ya hay una consulta abierta entre este paciente y doctor
        $existing = Consulta::where('doctor_id', $request->doctor_id)
            ->where('paciente_id', $pacienteId)
            ->where(function($q){ $q->whereNull('status')->orWhere('status','!=','finalizado'); })
            ->first();
        if ($existing) {
            if ($request->wantsJson() || $request->expectsJson() || str_contains(strtolower($request->header('accept')), 'application/json')) {
                return response()->json([
                    'error' => 'Ya tienes una consulta abierta con este doctor.',
                    'data' => [ 'id' => $existing->id ]
                ], 409);
            }
            return back()->with('error', 'Ya tienes una consulta abierta con este doctor.');
        }

        // Guardar el motivo en el campo 'mensaje' de la consulta (cabecera)
        $consulta = Consulta::create([
            'doctor_id' => $request->doctor_id,
            'paciente_id' => $pacienteId,
            'mensaje' => $request->motivo,
            'status' => 'nuevo',
        ]);

        // Registrar primer mensaje como historial (descripción)
        ConsultaMensaje::create([
            'consulta_id' => $consulta->id,
            'sender_type' => 'paciente',
            'body' => $request->descripcion,
        ]);

        // Si el cliente espera JSON, responder con JSON para flujos AJAX
        if ($request->wantsJson() || $request->expectsJson() || str_contains(strtolower($request->header('accept')), 'application/json')) {
            return response()->json([
                'data' => [
                    'id' => $consulta->id,
                    'status' => $consulta->status,
                    'motivo' => $consulta->mensaje,
                ]
            ], 201);
        }

        return back()->with('success', 'Su consulta ha sido enviada correctamente');
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

        ConsultaMensaje::create([
            'consulta_id' => $consulta->id,
            'sender_type' => 'doctor',
            'body' => $request->respuesta,
        ]);

        return back()->with('success', 'Respuesta enviada');
    }

    // Doctor finaliza una consulta: mover a "finalizadas"
    public function finalizar(Consulta $consulta)
    {
        $doctorId = Session::get('doctor_id');
        $pacienteId = Session::get('paciente_id');
        $authorized = ($doctorId && $consulta->doctor_id == $doctorId) || ($pacienteId && $consulta->paciente_id == $pacienteId);
        if (! $authorized) { abort(403); }

        $consulta->status = 'finalizado';
        $consulta->save();

        // Si es una petición AJAX/JSON, devolver respuesta JSON para evitar flashes del servidor
        if (request()->wantsJson() || request()->expectsJson() || str_contains(strtolower(request()->header('accept')), 'application/json')) {
            return response()->json([
                'data' => [
                    'id' => $consulta->id,
                    'status' => $consulta->status,
                ]
            ]);
        }

        return back()->with('success', 'Consulta finalizada');
    }

    // Eliminar definitivamente una consulta (solo finalizada)
    public function eliminar(Consulta $consulta)
    {
        $doctorId = Session::get('doctor_id');
        if (! $doctorId || $consulta->doctor_id != $doctorId) {
            abort(403);
        }

        if ($consulta->status !== 'finalizado') {
            return back()->with('error', 'Solo puedes eliminar consultas finalizadas');
        }

        $consulta->delete(); // cascada elimina mensajes
        return back()->with('success', 'Consulta eliminada');
    }

    // Paciente oculta una consulta finalizada solo para su vista
    public function ocultarParaPaciente(Consulta $consulta)
    {
        $pacienteId = Session::get('paciente_id');
        if (! $pacienteId || $consulta->paciente_id != $pacienteId) {
            abort(403);
        }

        if ($consulta->status !== 'finalizado') {
            return back()->with('error', 'Solo puedes ocultar consultas finalizadas');
        }

        // marcar como oculta para el paciente
        if (\Illuminate\Support\Facades\Schema::hasColumn('consultas','oculta_para_paciente')) {
            $consulta->oculta_para_paciente = true;
            $consulta->save();
        }

        if (request()->wantsJson() || request()->expectsJson() || str_contains(strtolower(request()->header('accept')), 'application/json')) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Consulta eliminada de tu vista');
    }

    // Obtener mensajes de una consulta (JSON)
    public function mensajes(Consulta $consulta)
    {
        $doctorId = Session::get('doctor_id');
        $pacienteId = Session::get('paciente_id');
        if (($doctorId && $consulta->doctor_id == $doctorId) || ($pacienteId && $consulta->paciente_id == $pacienteId)) {
            $data = $consulta->mensajes()->get()->map(function($m){
                return [
                    'id' => $m->id,
                    'sender' => $m->sender_type,
                    'body' => $m->body,
                    'created_at' => $m->created_at ? $m->created_at->timezone('America/El_Salvador')->format('Y-m-d H:i:s') : null,
                ];
            });
            return response()->json([
                'consulta' => [
                    'id' => $consulta->id,
                    'doctor_id' => $consulta->doctor_id,
                    'status' => $consulta->status,
                    'motivo' => $consulta->mensaje,
                ],
                'data' => $data
            ]);
        }
        abort(403);
    }

    // Enviar mensaje (doctor o paciente)
    public function enviarMensaje(Request $request, Consulta $consulta)
    {
        $request->validate(['body' => 'required|string|max:5000']);

        if ($consulta->status === 'finalizado') {
            return response()->json(['error' => 'La consulta ya está finalizada'], 422);
        }

        $doctorId = Session::get('doctor_id');
        $pacienteId = Session::get('paciente_id');

        $sender = null;
        if ($doctorId && $consulta->doctor_id == $doctorId) { $sender = 'doctor'; }
        if ($pacienteId && $consulta->paciente_id == $pacienteId) { $sender = 'paciente'; }
        if (!$sender) { abort(403); }

        $msg = ConsultaMensaje::create([
            'consulta_id' => $consulta->id,
            'sender_type' => $sender,
            'body' => $request->body,
        ]);

        return response()->json([
            'data' => [
                'id' => $msg->id,
                'sender' => $msg->sender_type,
                'body' => $msg->body,
                'created_at' => $msg->created_at ? $msg->created_at->timezone('America/El_Salvador')->format('Y-m-d H:i:s') : null,
            ]
        ]);
    }
}
