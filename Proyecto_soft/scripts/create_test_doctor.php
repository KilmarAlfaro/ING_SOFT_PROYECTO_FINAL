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
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;

// Create user
$email = 'testdoctor@example.local';
if (User::where('email', $email)->exists()) {
    echo "User with email $email already exists.\n";
    $user = User::where('email', $email)->first();
} else {
    $user = User::create([
        'name' => 'Dr Test',
        'email' => $email,
        'password' => Hash::make('secret123'),
    ]);
    echo "Created user id={$user->id}\n";
}

// Create doctor
if (Doctor::where('user_id', $user->id)->exists()) {
    echo "Doctor already linked to user id={$user->id}\n";
    $doctor = Doctor::where('user_id', $user->id)->first();
} else {
    $doctor = Doctor::create([
        'user_id' => $user->id,
        'nombre' => 'Dr Test',
        'apellido' => 'Testing',
        'correo' => $email,
        'telefono' => '12345678',
        'especialidad' => 'Testing',
        'numero_colegiado' => 'T-0001',
        'usuario' => $user->name,
        'password_hash' => Hash::make('secret123'),
        'direccion_clinica' => 'Calle Prueba 123',
        'estado' => 'activo',
    ]);
    echo "Created doctor id={$doctor->id} linked to user_id={$doctor->user_id}\n";
}

// Show inserted doctor row
$doc = Doctor::where('user_id', $user->id)->first();
print_r($doc->toArray());

$kernel->terminate($input, $status);
