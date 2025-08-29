<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/login/{tipo}', function ($tipo) {
    return view('login', ['tipo' => $tipo]);
})->name('login');
