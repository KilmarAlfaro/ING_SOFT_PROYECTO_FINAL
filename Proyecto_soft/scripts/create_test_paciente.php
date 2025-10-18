<?php
require __DIR__ . '/../vendor/autoload.php';

// Boot the framework
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput([]),
    new Symfony\Component\Console\Output\NullOutput
);

use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

$email = 'testpaciente@example.local';
if (User::where('email', $email)->exists()) {
    echo "User with email $email already exists.\n";
    $user = User::where('email', $email)->first();
} else {
    $user = User::create([
        'name' => 'Paciente Test',
        'email' => $email,
        'password' => Hash::make('secret123'),
    ]);
    echo "Created user id={$user->id}\n";
}

if (Paciente::where('correo', $email)->exists()) {
    echo "Paciente already linked to user id={$user->id}\n";
    $paciente = Paciente::where('correo', $email)->first();
} else {
    $paciente = Paciente::create([
        'nombre' => 'Paciente Test',
        'apellido' => 'Testing',
        'correo' => $email,
        'telefono' => '87654321',
        'numero_dui' => '11111111-1',
        'fecha_nacimiento' => '1990-01-01',
        'sexo' => 'Masculino',
        'password_hash' => Hash::make('secret123'),
    ]);
    echo "Created paciente id={$paciente->id}\n";
}

$kernel->terminate($input, $status);
