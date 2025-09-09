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

    <form action="{{ route('registroDoc.submit') }}" method="POST" class="formulario" autocomplete="off">
      @csrf

      <!-- DATOS PERSONALES -->
      <h2 class="subtitulo">Datos personales</h2>

      <label for="nombre">Nombre completo:</label>
      <input type="text" id="nombre" name="nombre" placeholder="Juan Pérez" required autocomplete="off">

      <label for="telefono">Número de teléfono:</label>
      <input type="text" id="telefono" name="telefono" placeholder="1234-5678" required autocomplete="off">

      <label for="dui">DUI:</label>
      <input type="text" id="dui" name="dui" placeholder="01234567-8" required autocomplete="off">

      <label for="especialidad">Especialidad:</label>
      <input type="text" id="especialidad" name="especialidad" placeholder="Cardiología" required autocomplete="off">

      <label for="direccion">Dirección de la clínica u hospital:</label>
      <input type="text" id="direccion" name="direccion" placeholder="Av. Principal #123, Ciudad" required autocomplete="off">

      <!-- CREDENCIALES -->
      <h2 class="subtitulo">Credenciales</h2>

      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required autocomplete="new-email">
      @if(session('email_existente'))
        <span class="mensaje-error">Este correo ya está registrado</span>
      @endif

      <label for="password">Contraseña:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required autocomplete="new-password">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword">
      </div>

      <label for="password_confirmation">Confirmar contraseña:</label>
      <div class="password-container">
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required autocomplete="new-password">

      </div>

      <!-- BOTONES -->
      <div class="acciones">
        <button type="submit" class="btn btn-primario">Registrarse</button>
        <a href="{{ url('/') }}" class="btn btn-secundario">Regresar</a>
      </div>
    </form>
  </div>

  <script>
    // Mostrar/ocultar contraseña principal
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    togglePassword.addEventListener('click', () => {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
    });

    // Mostrar/ocultar contraseña de confirmación
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirm = document.getElementById('password_confirmation');
    togglePasswordConfirm.addEventListener('click', () => {
      const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordConfirm.setAttribute('type', type);
    });
  </script>
</body>
</html>
