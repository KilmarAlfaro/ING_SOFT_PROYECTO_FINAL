<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\User;
use App\Rules\ValidDui;
use App\Services\DuiService;
use App\Services\EmailOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginRegistroController extends Controller
{
    public function __construct(private readonly EmailOtpService $otpService)
    {
    }

    public function preValidarPaciente(Request $request)
    {
        $validated = $this->validatePaciente($request, requireOtp: false);

        $this->otpService->send($validated['correo']);

        return response()->json([
            'message' => 'Datos validos. Hemos enviado un codigo de verificacion a tu correo.',
            'email' => $validated['correo'],
        ]);
    }

    public function preValidarDoctor(Request $request)
    {
        $validated = $this->validateDoctor($request, requireOtp: false);

        $this->otpService->send($validated['correo']);

        return response()->json([
            'message' => 'Datos validos. Hemos enviado un codigo de verificacion a tu correo.',
            'email' => $validated['correo'],
        ]);
    }

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
        if (! $paciente) {
            return back()->with('email_no_registrado', true);
        }

        $user = User::where('email', $credentials['email'])->where('role', 'paciente')->first();
        if ($user && is_null($user->email_verified_at)) {
            return back()->with('email_no_verificado', true)->with('pending_email', $credentials['email']);
        }

        if (! Hash::check($credentials['password'], $paciente->password_hash)) {
            return back()->with('password_incorrecta', true);
        }

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
        if (! $doctor) {
            return back()->with('email_no_registrado', true);
        }

        $user = User::where('email', $credentials['email'])->where('role', 'doctor')->first();
        if ($user && is_null($user->email_verified_at)) {
            return back()->with('email_no_verificado', true)->with('pending_email', $credentials['email']);
        }

        if (! Hash::check($credentials['password'], $doctor->password_hash)) {
            return back()->with('password_incorrecta', true);
        }

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
        $validated = $this->validatePaciente($request);

        if (! $this->otpService->isValid($validated['correo'], $validated['otp_code'])) {
            return back()->withErrors(['otp_code' => 'El codigo ingresado es invalido o expiro.'])->withInput();
        }

        $formattedDui = DuiService::format($validated['numero_dui']);
        $fullName = trim($validated['nombre'] . ' ' . ($validated['apellido'] ?? ''));

        $user = User::create([
            'name' => $fullName,
            'telefono' => $validated['telefono'],
            'dui' => $formattedDui,
            'direccion' => $validated['direccion'] ?? null,
            'email' => $validated['correo'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'role' => 'paciente',
        ]);

        try {
            Paciente::create([
                'user_id' => $user->id,
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'correo' => $validated['correo'],
                'telefono' => $validated['telefono'],
                'numero_dui' => $formattedDui,
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'sexo' => $validated['sexo'],
                'direccion' => $validated['direccion'] ?? null,
                'password_hash' => Hash::make($validated['password']),
            ]);
        } catch (\Throwable $th) {
            Log::warning('No se pudo sincronizar paciente legacy: ' . $th->getMessage());
        }

        $this->otpService->consume($validated['correo']);

        return redirect()->route('loginPac')
            ->with('status', 'Cuenta creada y verificada. Ahora puedes iniciar sesion.');
    }

    /**
     * Registra a un nuevo doctor con validaciones de DUI y campos complementarios.
     */
    public function registroDoc(Request $request)
    {
        $validated = $this->validateDoctor($request);

        if (! $this->otpService->isValid($validated['correo'], $validated['otp_code'])) {
            return back()->withErrors(['otp_code' => 'El codigo ingresado es invalido o expiro.'])->withInput();
        }

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
            'email_verified_at' => now(),
            'role' => 'doctor',
        ]);

        try {
            Doctor::create([
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
        } catch (\Throwable $th) {
            Log::warning('No se pudo sincronizar doctor legacy: ' . $th->getMessage());
        }

        $this->otpService->consume($validated['correo']);

        return redirect()->route('loginDoc')
            ->with('status', 'Cuenta creada y verificada. Ahora puedes iniciar sesion.');
    }

    private function pacienteMessages(): array
    {
            return [
            'numero_dui.unique' => 'El DUI ingresado ya esta registrado.',
            'correo.unique' => 'El correo ingresado ya esta registrado.',
            'password.min' => 'Se necesita al menos 6 caracteres en tu contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    private function pacienteRules(bool $requireOtp = true): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date',
                'numero_dui' => ['required', 'string', new ValidDui, 'unique:users,dui', 'unique:pacientes,numero_dui'],
            'sexo' => 'required|string|in:Masculino,Femenino',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'required|email|unique:users,email|unique:pacientes,correo',
            'password' => 'required|string|min:6|confirmed',
        ];

        if ($requireOtp) {
            $rules['otp_code'] = 'required|digits:4';
        }

        return $rules;
    }

    private function validatePaciente(Request $request, bool $requireOtp = true): array
    {
        return $request->validate($this->pacienteRules($requireOtp), $this->pacienteMessages());
    }

    private function doctorMessages(): array
    {
        return [
            'numero_dui.unique' => 'El DUI ingresado ya esta registrado.',
            'correo.unique' => 'El correo ingresado ya esta registrado.',
            'password.min' => 'Se necesita al menos 6 caracteres en tu contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    private function doctorRules(bool $requireOtp = true): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
                'numero_dui' => ['required', 'string', new ValidDui, 'unique:users,dui', 'unique:doctors,numero_dui'],
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|string|in:Masculino,Femenino',
            'especialidad' => 'required|string|max:255',
            'especialidad_otro' => 'nullable|string|max:255',
            'numero_colegiado' => 'nullable|string|max:50',
            'direccion_clinica' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'correo' => 'required|email|unique:users,email|unique:doctors,correo',
            'password' => 'required|string|min:6|confirmed',
        ];

        if ($requireOtp) {
            $rules['otp_code'] = 'required|digits:4';
        }

        return $rules;
    }

    private function validateDoctor(Request $request, bool $requireOtp = true): array
    {
        return $request->validate($this->doctorRules($requireOtp), $this->doctorMessages());
    }
}
