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
        Schema::table('doctors', function (Blueprint $table) {
            if (! Schema::hasColumn('doctors', 'numero_dui')) {
                $table->string('numero_dui')->nullable()->after('numero_colegiado');
            }
            if (! Schema::hasColumn('doctors', 'fecha_nacimiento')) {
                $table->date('fecha_nacimiento')->nullable()->after('numero_dui');
            }
            if (! Schema::hasColumn('doctors', 'genero')) {
                $table->string('genero')->nullable()->after('fecha_nacimiento');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (Schema::hasColumn('doctors', 'numero_dui')) {
                $table->dropColumn('numero_dui');
            }
            if (Schema::hasColumn('doctors', 'fecha_nacimiento')) {
                $table->dropColumn('fecha_nacimiento');
            }
            if (Schema::hasColumn('doctors', 'genero')) {
                $table->dropColumn('genero');
            }
        });
    }
};
