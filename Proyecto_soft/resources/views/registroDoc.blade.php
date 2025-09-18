<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Doctor</title>
  <link rel="stylesheet" href="{{ asset('css/registroDoc.css') }}">
</head>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Doctor</title>
  <link rel="stylesheet" href="{{ asset('css/registroDoc.css') }}">
</head>
<body>
  <div class="registro-contenedor">
    <h1 class="titulo">Registro de Doctor</h1>

    <form action="{{ route('doctores.store') }}" method="POST" class="formulario" autocomplete="off">
      @csrf

      <!-- DATOS PERSONALES -->
      <h2 class="subtitulo">Datos personales</h2>

      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Juan" required>

      <label for="apellido">Apellido:</label>
      <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" placeholder="Pérez" required>

      <label for="telefono">Número de teléfono:</label>
      <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="1234-5678" required>

      <label for="especialidad">Especialidad:</label>
      <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad') }}" placeholder="Cardiología" required>

      <label for="numero_colegiado">Número colegiado:</label>
      <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado') }}" placeholder="123456" required>

      <label for="direccion_clinica">Dirección de la clínica u hospital:</label>
      <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica') }}" placeholder="Av. Principal #123, Ciudad" required>

      <!-- CREDENCIALES -->
      <h2 class="subtitulo">Credenciales</h2>

      <label for="correo">Correo electrónico:</label>
      <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="ejemplo@correo.com" required>
      @error('correo')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="usuario">Usuario:</label>
      <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="usuario123" required>
      @error('usuario')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="password">Contraseña:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required>
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword">
      </div>

      <!-- ESTADO -->
      <h2 class="subtitulo">Estado</h2>
      <label for="estado">Estado:</label>
      <select id="estado" name="estado" required>
        <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
        <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
      </select>

      <!-- BOTONES -->
      <div class="acciones">
        <button type="submit" class="btn btn-primario">Registrarse</button>
        <a href="{{ url('/') }}" class="btn btn-secundario">Regresar</a>
      </div>
    </form>
  </div>

  <script>
    // Mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    togglePassword.addEventListener('click', () => {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
    });
  </script>
</body>
</html>
