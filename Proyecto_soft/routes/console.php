<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Utilidad: eliminar todas las consultas y reiniciar el contador de IDs a 1
Artisan::command('consultas:reset', function () {
    DB::table('consultas')->delete();
    try {
        // Resetear el autoincrement en SQLite (si aplica)
        DB::statement('DELETE FROM sqlite_sequence WHERE name="consultas"');
    } catch (\Throwable $e) {
        // Ignorar si no existe sqlite_sequence o si no aplica
    }
    $this->info('Consultas eliminadas y contador reiniciado.');
})->purpose('Vaciar la tabla consultas y reiniciar IDs');
