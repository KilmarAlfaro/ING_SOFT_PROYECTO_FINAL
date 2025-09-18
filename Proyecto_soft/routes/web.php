<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pacienteController;
use App\Http\Controllers\LoginRegistroController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\doctorController;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');

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

// Login
Route::post('/login/paciente', [LoginRegistroController::class, 'loginPac'])->name('loginPac.submit');
Route::post('/login/doctor', [LoginRegistroController::class, 'loginDoc'])->name('loginDoc.submit');

// Ruta para procesar el login
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
Route::get('/paciente/{paciente}', [App\Http\Controllers\pacienteController::class, 'show'])->name('paciente.show');
Route::get('/doctores/{doctor}', [App\Http\Controllers\doctorController::class, 'show'])->name('doctores.show');

//resourses
//Route::resource('paciente', App\Http\Controllers\pacienteController::class);
Route::get('/perfil-doc', function () {
    return view('perfilDoc');
})->name('perfilDoc');


use App\Http\Controllers\AuthController;

// Logout Doctor/Paciente
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


