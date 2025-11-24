<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono', 25)->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'dui')) {
                $table->string('dui', 10)->nullable()->unique()->after('telefono');
            }
            if (! Schema::hasColumn('users', 'direccion')) {
                $table->string('direccion')->nullable()->after('dui');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('direccion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('users', 'dui')) {
                $table->dropUnique('users_dui_unique');
                $table->dropColumn('dui');
            }
            if (Schema::hasColumn('users', 'direccion')) {
                $table->dropColumn('direccion');
            }
            // keep role if other migrations rely on it
        });
    }
};
