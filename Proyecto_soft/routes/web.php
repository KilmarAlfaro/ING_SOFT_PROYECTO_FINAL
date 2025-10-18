<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pacienteController;
use App\Http\Controllers\LoginRegistroController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\doctorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilDoctorController;
use App\Http\Controllers\PerfilPacienteController;

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

// Perfil doctor (no se toca)
Route::get('/perfil-doc', [PerfilDoctorController::class, 'show'])->name('perfil.doctor');
Route::post('/perfil-doc', [PerfilDoctorController::class, 'update'])->name('perfil.doctor.update');

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
