<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'especialidad',
        'numero_colegiado',
        'usuario',
        'password_hash',
        'direccion_clinica',
        'estado',
        'fecha_creacion',
        'ultimo_login',
    ];
}
