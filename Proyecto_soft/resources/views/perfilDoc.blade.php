<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Doctor</title>
    <link rel="stylesheet" href="{{ asset('css/perfilDoc.css') }}">
</head>
<body>
    <div class="perfil-container">
        <h1>Perfil del Doctor</h1>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('perfil.doctor.update') }}" method="POST" autocomplete="off">
            @csrf

            <h2>Datos personales</h2>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $doctors->nombre) }}" required>
            @error('nombre') <span class="error">{{ $message }}</span> @enderror

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $doctors->apellido) }}" required>
            @error('apellido') <span class="error">{{ $message }}</span> @enderror

            <label for="telefono">Número de teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $doctors->telefono) }}" required>
            @error('telefono') <span class="error">{{ $message }}</span> @enderror

            <label for="especialidad">Especialidad:</label>
            <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad', $doctors->especialidad) }}" required>
            @error('especialidad') <span class="error">{{ $message }}</span> @enderror

            <label for="numero_colegiado">Número colegiado:</label>
            <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado', $doctors->numero_colegiado) }}" required>
            @error('numero_colegiado') <span class="error">{{ $message }}</span> @enderror

            <label for="direccion_clinica">Dirección de la clínica:</label>
            <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica', $doctors->direccion_clinica) }}" required>
            @error('direccion_clinica') <span class="error">{{ $message }}</span> @enderror

            <h2>Credenciales</h2>

            <label for="correo">Correo electrónico:</label>
            <input type="email" id="correo" name="correo" value="{{ old('correo', Auth::user()->email) }}" required>
            @error('correo') <span class="error">{{ $message }}</span> @enderror

            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" value="{{ old('usuario', Auth::user()->name) }}" required>
            @error('usuario') <span class="error">{{ $message }}</span> @enderror

            <label for="password">Nueva contraseña (opcional):</label>
            <input type="password" id="password" name="password">
            @error('password') <span class="error">{{ $message }}</span> @enderror

            <button type="submit">Actualizar Perfil</button>
        </form>
    </div>
</body>
</html>
