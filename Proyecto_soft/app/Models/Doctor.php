<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';

    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'especialidad',
        'numero_colegiado',
        // 'usuario' is no longer required at registration but keep column if present
        'usuario',
        'password_hash',
        'direccion_clinica',
        'estado',
        'numero_dui',
    'fecha_nacimiento',
    'sexo',
        'foto_perfil',
        'fecha_creacion',
        'ultimo_login',
    ];

    /**
     * Relacion: doctor pertenece a un usuario (auth)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
