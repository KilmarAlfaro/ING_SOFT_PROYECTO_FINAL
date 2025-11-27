<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';

    protected $fillable = [
        'doctor_id',
        'paciente_id',
        'mensaje',
        'respuesta',
        'status',
        'tag_label',
        'tag_color',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function mensajes()
    {
        return $this->hasMany(ConsultaMensaje::class)->orderBy('created_at', 'asc');
    }
}
