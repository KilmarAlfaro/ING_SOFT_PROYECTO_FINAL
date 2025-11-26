<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pacienteController;
use App\Http\Controllers\LoginRegistroController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\doctorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilDoctorController;
use App\Http\Controllers\PerfilPacienteController;
use App\Http\Controllers\MediaController;
use App\Http\Middleware\RequireSessionOrAuth;

/* Rutas públicas */
Route::get('/', function () {
    return view('inicio');
})->name('inicio');

Route::get('/mainpage', function () {
    return view('mainpage');
})->name('mainpage');

/* LOGIN Paciente */
Route::get('/login/paciente', function () {
    return view('loginPac');
})->name('loginPac');

/* LOGIN Doctor */
Route::get('/login/doctor', function () {
    return view('loginDoc');
})->name('loginDoc');

/* REGISTRO Paciente */
Route::get('/registro/paciente', function () {
    return view('registroPac');
})->name('registroPac');

/* REGISTRO Doctor */
Route::get('/registro/doctor', function () {
    return view('registroDoc');
})->name('registroDoc');

// Procesar login Paciente
Route::post('/login/paciente', [App\Http\Controllers\loginController::class, 'loginPac'])->name('loginPac.submit');

// Procesar login Doctor
Route::post('/login/doctor', [App\Http\Controllers\loginController::class, 'loginDoc'])->name('loginDoc.submit');

// Ruta para procesar el login genérico (si la usas)
Route::post('/login', [loginController::class, 'login']);

// Registro
Route::post('/registro/paciente', [LoginRegistroController::class, 'registroPac'])->name('registroPac.submit');
Route::post('/registro/doctor', [LoginRegistroController::class, 'registroDoc'])->name('registroDoc.submit');

// Protected routes: require session or auth
Route::middleware([RequireSessionOrAuth::class])->group(function () {
    // Rutas main
    Route::get('/main/doctor', function() {
        return view('mainDoc');
    })->name('mainDoc');

    Route::get('/main/paciente', function() {
        return view('mainPac');
    })->name('mainPac');

    // Ruta stores
    Route::post('/paciente', [App\Http\Controllers\pacienteController::class, 'store'])->name('paciente.store');
    Route::post('/doctores', [App\Http\Controllers\doctorController::class, 'store'])->name('doctores.store');

    // ruta para crear
    Route::get('/paciente/create', [App\Http\Controllers\pacienteController::class, 'create'])->name('paciente.create');
    Route::get('/doctores/create', [App\Http\Controllers\doctorController::class, 'create'])->name('doctores.create');

    // show
    Route::get('/doctores/{doctor}', [App\Http\Controllers\doctorController::class, 'show'])->name('doctores.show');

    // Logout Doctor/Paciente
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Perfil doctor
    Route::get('/perfil-doc', [PerfilDoctorController::class, 'show'])->name('perfil.doctor');
    Route::post('/perfil-doc', [PerfilDoctorController::class, 'update'])->name('perfil.doctor.update');
    Route::delete('/perfil-doc', [PerfilDoctorController::class, 'destroy'])->name('perfil.doctor.destroy');

    // buscar doctor
    Route::get('/buscar-doctor', [doctorController::class, 'buscar'])->name('buscar.doctor');

    // Consultas
    Route::post('/consultas', [App\Http\Controllers\ConsultaController::class, 'store'])->name('consultas.store');
    Route::get('/consultas/doctor', [App\Http\Controllers\ConsultaController::class, 'doctorIndex'])->name('consultas.doctor');
    Route::get('/consultas/paciente', [App\Http\Controllers\ConsultaController::class, 'pacienteIndex'])->name('consultas.paciente');
    Route::post('/consultas/{consulta}/responder', [App\Http\Controllers\ConsultaController::class, 'responder'])->name('consultas.responder');
    Route::post('/consultas/{consulta}/finalizar', [App\Http\Controllers\ConsultaController::class, 'finalizar'])->name('consultas.finalizar');
    Route::post('/consultas/{consulta}/ocultar-paciente', [App\Http\Controllers\ConsultaController::class, 'ocultarParaPaciente'])->name('consultas.ocultarPaciente');
    Route::delete('/consultas/{consulta}', [App\Http\Controllers\ConsultaController::class, 'eliminar'])->name('consultas.eliminar');
    Route::get('/consultas/{consulta}/mensajes', [App\Http\Controllers\ConsultaController::class, 'mensajes'])->name('consultas.mensajes');
    Route::post('/consultas/{consulta}/mensajes', [App\Http\Controllers\ConsultaController::class, 'enviarMensaje'])->name('consultas.mensajes.enviar');

    // consulta
    Route::get('/consulta-doctor/{id}', [doctorController::class, 'consulta'])->name('consulta.doctor');

    // RUTAS PERFIL PACIENTE
    Route::get('/perfil/paciente', [PerfilPacienteController::class, 'edit'])->name('perfil.paciente');
    Route::post('/perfil/paciente', [PerfilPacienteController::class, 'update'])->name('perfil.paciente.update');
});

// Asegura que /login redirija a la pantalla de login de paciente
Route::get('/login', function () {
    return redirect()->route('loginPac');
})->name('login');

// buscar doctor
Route::get('/buscar-doctor', [doctorController::class, 'buscar'])->name('buscar.doctor');

// consulta
Route::get('/consulta-doctor/{id}', [doctorController::class, 'consulta'])->name('consulta.doctor');

/*
  RUTAS PERFIL PACIENTE
  -- No usar middleware auth porque el login personalizado usa Session.
  -- El controlador validará la sesión.
*/
Route::get('/perfil/paciente', [PerfilPacienteController::class, 'edit'])->name('perfil.paciente');
Route::post('/perfil/paciente', [PerfilPacienteController::class, 'update'])->name('perfil.paciente.update');
Route::delete('/perfil/paciente', [PerfilPacienteController::class, 'destroy'])->name('perfil.paciente.destroy');

// Avatares servidos desde BD o almacenamiento (acceso público)
Route::get('/media/doctores/{id}', [MediaController::class, 'doctorAvatar'])->name('avatar.doctor');
Route::get('/media/pacientes/{id}', [MediaController::class, 'pacienteAvatar'])->name('avatar.paciente');
