<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\User;
use App\Rules\ValidDui;
use App\Services\DuiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginRegistroController extends Controller
{
	/**
	 * Autentica un paciente usando las credenciales legadas o la tabla users.
	 */
	public function loginPac(Request $request)
	{
		$credentials = $request->validate([
			'email' => 'required|email',
			'password' => 'required|string',
		]);

		$paciente = Paciente::where('correo', $credentials['email'])->first();
		if (!$paciente) {
			return back()->with('email_no_registrado', true);
		}

		if (!Hash::check($credentials['password'], $paciente->password_hash)) {
			return back()->with('password_incorrecta', true);
		}

		$user = User::where('email', $credentials['email'])->where('role', 'paciente')->first();
		if ($user) {
			Auth::login($user);
		} else {
			Session::put('paciente_id', $paciente->id);
			Session::put('paciente_nombre', $paciente->nombre);
			Session::put('paciente_sexo', $paciente->sexo ?? null);
		}

		return redirect()->route('mainPac');
	}

	/**
	 * Autentica un doctor usando las credenciales legadas o la tabla users.
	 */
	public function loginDoc(Request $request)
	{
		$credentials = $request->validate([
			'email' => 'required|email',
			'password' => 'required|string',
		]);

		$doctor = Doctor::where('correo', $credentials['email'])->first();
		if (!$doctor) {
			return back()->with('email_no_registrado', true);
		}

		if (!Hash::check($credentials['password'], $doctor->password_hash)) {
			return back()->with('password_incorrecta', true);
		}

		$user = User::where('email', $credentials['email'])->where('role', 'doctor')->first();
		if ($user) {
			Auth::login($user);
		} else {
			Session::put('doctor_id', $doctor->id);
			Session::put('doctor_nombre', $doctor->nombre);
			Session::put('doctor_sexo', $doctor->sexo ?? null);
		}

		return redirect()->route('mainDoc');
	}

	/**
	 * Registra a un nuevo paciente con validaciones de DUI y sincroniza legacy tables.
	 */
	public function registroPac(Request $request)
	{
		$messages = [
			'numero_dui.regex' => 'El DUI debe escribirse como 8 dígitos, un guion y el dígito verificador (########-#).',
			'numero_dui.unique' => 'El DUI ingresado ya está registrado.',
		];

		$validated = $request->validate([
			'nombre' => 'required|string|max:255',
			'apellido' => 'nullable|string|max:255',
			'telefono' => 'required|string|max:20',
			'fecha_nacimiento' => 'nullable|date',
			'numero_dui' => ['required', 'string', 'regex:/^\d{8}-?\d$/', new ValidDui, 'unique:users,dui', 'unique:pacientes,numero_dui'],
			'sexo' => 'nullable|string|in:Masculino,Femenino',
			'direccion' => 'nullable|string|max:255',
			'correo' => 'required|email|unique:users,email|unique:pacientes,correo',
			'password' => 'required|string|min:6|confirmed',
		], $messages);

		$formattedDui = DuiService::format($validated['numero_dui']);
		$fullName = trim($validated['nombre'] . ' ' . ($validated['apellido'] ?? ''));

		$user = User::create([
			'name' => $fullName,
			'telefono' => $validated['telefono'],
			'dui' => $formattedDui,
			'direccion' => $validated['direccion'] ?? null,
			'email' => $validated['correo'],
			'password' => Hash::make($validated['password']),
			'role' => 'paciente',
		]);

		Auth::login($user);

		try {
			$paciente = Paciente::create([
				'user_id' => $user->id,
				'nombre' => $validated['nombre'],
				'apellido' => $validated['apellido'] ?? null,
				'correo' => $validated['correo'],
				'telefono' => $validated['telefono'],
				'numero_dui' => $formattedDui,
				'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
				'sexo' => $validated['sexo'] ?? null,
				'direccion' => $validated['direccion'] ?? null,
				'password_hash' => Hash::make($validated['password']),
			]);

			Session::put('paciente_id', $paciente->id);
			Session::put('paciente_nombre', $paciente->nombre);
			Session::put('paciente_sexo', $paciente->sexo ?? null);
		} catch (\Throwable $th) {
			Log::warning('No se pudo sincronizar paciente legacy: ' . $th->getMessage());
			Session::put('paciente_nombre', $fullName);
			Session::put('paciente_sexo', $validated['sexo'] ?? null);
		}

		return redirect()->route('mainPac');
	}

	/**
	 * Registra a un nuevo doctor con validaciones de DUI y campos complementarios.
	 */
	public function registroDoc(Request $request)
	{
		$messages = [
			'numero_dui.regex' => 'El DUI debe escribirse como 8 dígitos, un guion y el dígito verificador (########-#).',
			'numero_dui.unique' => 'El DUI ingresado ya está registrado.',
		];

		$validated = $request->validate([
			'nombre' => 'required|string|max:255',
			'apellido' => 'nullable|string|max:255',
			'telefono' => 'required|string|max:20',
			'numero_dui' => ['required', 'string', 'regex:/^\d{8}-?\d$/', new ValidDui, 'unique:users,dui', 'unique:doctors,numero_dui'],
			'fecha_nacimiento' => 'nullable|date',
			'sexo' => 'nullable|string|in:Masculino,Femenino',
			'especialidad' => 'required|string|max:255',
			'especialidad_otro' => 'nullable|string|max:255',
			'numero_colegiado' => 'nullable|string|max:50',
			'direccion_clinica' => 'required|string|max:255',
			'descripcion' => 'nullable|string',
			'correo' => 'required|email|unique:users,email|unique:doctors,correo',
			'password' => 'required|string|min:6|confirmed',
		], $messages);

		$formattedDui = DuiService::format($validated['numero_dui']);
		$fullName = trim($validated['nombre'] . ' ' . ($validated['apellido'] ?? ''));
		$finalEspecialidad = ($validated['especialidad'] === 'Otro' && $validated['especialidad_otro'])
			? $validated['especialidad_otro']
			: $validated['especialidad'];

		$user = User::create([
			'name' => $fullName,
			'telefono' => $validated['telefono'],
			'dui' => $formattedDui,
			'direccion' => $validated['direccion_clinica'],
			'email' => $validated['correo'],
			'password' => Hash::make($validated['password']),
			'role' => 'doctor',
		]);

		Auth::login($user);

		try {
			$doctor = Doctor::create([
				'user_id' => $user->id,
				'nombre' => $validated['nombre'],
				'apellido' => $validated['apellido'] ?? null,
				'correo' => $validated['correo'],
				'telefono' => $validated['telefono'],
				'numero_dui' => $formattedDui,
				'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
				'sexo' => $validated['sexo'] ?? null,
				'especialidad' => $finalEspecialidad,
				'numero_colegiado' => $validated['numero_colegiado'] ?? 'N/A',
				'direccion_clinica' => $validated['direccion_clinica'],
				'descripcion' => $validated['descripcion'] ?? null,
				'password_hash' => Hash::make($validated['password']),
				'estado' => 'activo',
			]);

			Session::put('doctor_id', $doctor->id);
			Session::put('doctor_nombre', $doctor->nombre);
			Session::put('doctor_sexo', $doctor->sexo ?? null);
		} catch (\Throwable $th) {
			Log::warning('No se pudo sincronizar doctor legacy: ' . $th->getMessage());
			Session::put('doctor_nombre', $fullName);
			Session::put('doctor_sexo', $validated['sexo'] ?? null);
		}

		return redirect()->route('mainDoc');
	}
}


