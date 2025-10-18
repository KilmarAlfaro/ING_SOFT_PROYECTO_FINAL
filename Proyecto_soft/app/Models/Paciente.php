<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'sexo',
        'correo',
        'telefono',
        'direccion',
        'numero_dui',
        'password_hash',
        'fecha_creacion',
        'foto_perfil',
    ];
}
