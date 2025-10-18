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
            if (Schema::hasColumn('doctors', 'usuario')) {
                // SQLite doesn't support dropping a column if an index still exists,
                // drop the unique index first when using sqlite.
                try {
                    if (Schema::getConnection()->getDriverName() === 'sqlite') {
                        \DB::statement('DROP INDEX IF EXISTS doctors_usuario_unique');
                    } else {
                        // For other drivers, try to drop the unique constraint if present
                        if (Schema::hasColumn('doctors', 'usuario')) {
                            $table->dropUnique(['usuario']);
                        }
                    }
                } catch (\Exception $e) {
                    // ignore errors dropping index - we'll still attempt to drop column
                }

                // Now drop the column if it still exists
                try {
                    $table->dropColumn('usuario');
                } catch (\Exception $e) {
                    // Some DB drivers (older sqlite) may not support dropColumn via blueprint;
                    // in that case we'll let the migration continue and handle schema manually.
                }
            }

            // Rename or add sexo if genero exists
            if (Schema::hasColumn('doctors', 'genero') && ! Schema::hasColumn('doctors', 'sexo')) {
                $table->string('sexo')->nullable()->after('fecha_nacimiento');
                // copy values from genero -> sexo will be done outside the schema closure
            }
        });

        // After structural change, copy genero values to sexo (if genero existed)
        if (Schema::hasColumn('doctors', 'genero') && Schema::hasColumn('doctors', 'sexo')) {
            try {
                \DB::statement('UPDATE doctors SET sexo = genero WHERE sexo IS NULL');
            } catch (\Exception $e) {
                // ignore: if update fails, it will remain nullable and can be fixed manually
            }

            // attempt to drop the old genero column
            try {
                Schema::table('doctors', function (Blueprint $table) {
                    if (Schema::hasColumn('doctors', 'genero')) {
                        $table->dropColumn('genero');
                    }
                });
            } catch (\Exception $e) {
                // ignore failures dropping column on sqlite dialects
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (! Schema::hasColumn('doctors', 'usuario')) {
                $table->string('usuario')->nullable()->after('numero_colegiado');
            }
            if (! Schema::hasColumn('doctors', 'genero') && Schema::hasColumn('doctors', 'sexo')) {
                $table->string('genero')->nullable()->after('fecha_nacimiento');
                \DB::statement('UPDATE doctors SET genero = sexo WHERE genero IS NULL');
                $table->dropColumn('sexo');
            }
        });
    }
};
