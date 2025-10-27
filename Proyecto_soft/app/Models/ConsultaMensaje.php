<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultaMensaje extends Model
{
    use HasFactory;

    protected $table = 'consulta_mensajes';

    protected $fillable = [
        'consulta_id',
        'sender_type',
        'body',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }
}
