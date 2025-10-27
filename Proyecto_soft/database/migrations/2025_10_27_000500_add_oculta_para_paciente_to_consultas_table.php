<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('consultas') && !Schema::hasColumn('consultas', 'oculta_para_paciente')) {
            Schema::table('consultas', function (Blueprint $table) {
                $table->boolean('oculta_para_paciente')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('consultas') && Schema::hasColumn('consultas', 'oculta_para_paciente')) {
            Schema::table('consultas', function (Blueprint $table) {
                $table->dropColumn('oculta_para_paciente');
            });
        }
    }
};
