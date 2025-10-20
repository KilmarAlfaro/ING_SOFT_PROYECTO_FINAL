<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('doctors', 'descripcion')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->text('descripcion')->nullable()->after('foto_perfil');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('doctors', 'descripcion')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }
    }
};
