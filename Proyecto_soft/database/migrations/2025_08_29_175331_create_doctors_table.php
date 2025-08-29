<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->string('telefono');
            $table->string('especialidad');
            $table->string('numero_colegiado');
            $table->string('usuario')->unique();
            $table->string('password_hash');
            $table->string('direccion_clinica');
            $table->enum('estado', ['activo', 'inactivo']); // Ajusta según tus estados
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('ultimo_login')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
