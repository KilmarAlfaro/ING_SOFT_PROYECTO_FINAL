<?php

use Illuminate\Support\Facades\Route;

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
Route::post('/login/paciente', [App\Http\Controllers\LoginController::class, 'loginPac'])->name('loginPac.submit');

// Procesar login Doctor
Route::post('/login/doctor', [App\Http\Controllers\LoginController::class, 'loginDoc'])->name('loginDoc.submit');

// Login
Route::post('/login/paciente', [LoginRegistroController::class, 'loginPac'])->name('loginPac.submit');
Route::post('/login/doctor', [LoginRegistroController::class, 'loginDoc'])->name('loginDoc.submit');

// Registro
Route::post('/registro/paciente', [LoginRegistroController::class, 'registroPac'])->name('registroPac.submit');
Route::post('/registro/doctor', [LoginRegistroController::class, 'registroDoc'])->name('registroDoc.submit');

Route::get('/main/doctor', function() {
    return view('mainDoc');
})->name('mainDoc');


Route::get('/perfil-doc', function () {
    return view('perfilDoc');
})->name('perfilDoc');