<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>
<body>
  <div class="login-contenedor">
    <h1 class="titulo">Iniciar Sesión</h1>

    <form action="#" method="POST" class="formulario">
      @csrf

      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>

      <label for="password">Contraseña:</label>
      <input type="password" id="password" name="password" placeholder="********" required>

      <div class="acciones">
        <a href="{{ url('/') }}" class="btn btn-secundario">Regresar</a>
        <a href="{{ route('registroPac') }}" class="btn btn-secundario">Crear cuenta</a>
        <button type="submit" class="btn btn-primario">Iniciar sesión</button>
      </div>
    </form>
  </div>
</body>
</html>
