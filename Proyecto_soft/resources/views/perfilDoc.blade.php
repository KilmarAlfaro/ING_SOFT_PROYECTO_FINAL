<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Doctor</title>
    <link rel="stylesheet" href="{{ asset('css/perfilDoc.css') }}">
</head>
<body>
    <div class="container">
        <div class="meta mb-8">
            <a href="{{ route('mainDoc') }}" class="btn secondary" title="Volver al inicio">◀ Volver</a>
            <h1>Mi perfil</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('perfil.doctor.update') }}" method="POST">
            @csrf

            <div class="grid">
                <div class="form-row">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $doctor->nombre ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $doctor->apellido ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" value="{{ old('correo', $doctor->correo ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $doctor->telefono ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="especialidad">Especialidad</label>
                    <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad', $doctor->especialidad ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="numero_colegiado">Número de colegiado</label>
                    <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado', $doctor->numero_colegiado ?? '') }}" required>
                </div>

                <div class="form-row full-width">
                    <label for="direccion_clinica">Dirección de la clínica</label>
                    <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica', $doctor->direccion_clinica ?? '') }}" required>
                </div>

                <div class="full-width"><hr></div>

                <div class="full-width"><h2>Datos de cuenta</h2></div>

                <div class="form-row">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" value="{{ old('usuario', optional($doctor->user)->name ?? $doctor->usuario ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="password">Nueva contraseña (opcional)</label>
                    <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                </div>

                <div class="full-width footer-actions">
                    <button type="submit" class="btn">Actualizar perfil</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
