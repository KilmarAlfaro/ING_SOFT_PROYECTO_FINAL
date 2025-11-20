<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (! Schema::hasColumn('pacientes', 'foto_perfil_blob')) {
                $table->binary('foto_perfil_blob')->nullable()->after('foto_perfil');
            }
            if (! Schema::hasColumn('pacientes', 'foto_perfil_mime')) {
                $table->string('foto_perfil_mime', 100)->nullable()->after('foto_perfil_blob');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (Schema::hasColumn('pacientes', 'foto_perfil_mime')) {
                $table->dropColumn('foto_perfil_mime');
            }
            if (Schema::hasColumn('pacientes', 'foto_perfil_blob')) {
                $table->dropColumn('foto_perfil_blob');
            }
        });
    }
};
