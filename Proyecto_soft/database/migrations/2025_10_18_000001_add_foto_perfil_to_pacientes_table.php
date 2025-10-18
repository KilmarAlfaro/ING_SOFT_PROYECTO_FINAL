<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (! Schema::hasColumn('pacientes', 'foto_perfil')) {
                $table->string('foto_perfil')->nullable()->after('password_hash');
            }
        });
    }

    public function down()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            if (Schema::hasColumn('pacientes', 'foto_perfil')) {
                $table->dropColumn('foto_perfil');
            }
        });
    }
};
