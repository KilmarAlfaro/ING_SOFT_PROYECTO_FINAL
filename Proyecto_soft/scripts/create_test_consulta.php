<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput([]),
    new Symfony\Component\Console\Output\NullOutput
);

use App\Models\Consulta;

$consulta = Consulta::create([
    'doctor_id' => 9,
    'paciente_id' => 9,
    'mensaje' => 'Hola doctor, ¿está disponible para una consulta la próxima semana?'
]);

echo "Created consulta id={$consulta->id}\n";

$kernel->terminate($input, $status);
