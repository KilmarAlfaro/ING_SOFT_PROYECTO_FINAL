<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consulta_mensajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consulta_id');
            $table->enum('sender_type', ['doctor', 'paciente']);
            $table->text('body');
            $table->timestamps();

            $table->foreign('consulta_id')->references('id')->on('consultas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_mensajes');
    }
};
