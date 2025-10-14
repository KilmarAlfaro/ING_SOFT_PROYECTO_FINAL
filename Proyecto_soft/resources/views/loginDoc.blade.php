<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Doctor</title>
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <style>
    .password-container {
      position: relative;
      display: flex;
      align-items: center;
    }
    .password-container img {
      position: absolute;
      right: 10px;
      cursor: pointer;
      width: 24px;
      height: 24px;
    }
    .mensaje-error {
      color: red;
      font-size: 0.9rem;
      margin-top: -5px;
      margin-bottom: 5px;
    }
    .mensaje-registro {
      margin-top: 10px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
  </style>
</head>
<body>
  <div class="login-contenedor">
    <h1 class="titulo">Iniciar Sesión</h1>

    <form action="{{ route('loginDoc.submit') }}" method="POST" class="formulario" autocomplete="off">
      @csrf

      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required autocomplete="on">

      @if(session('email_no_registrado'))
        <div class="mensaje-registro">
          <span class="mensaje-error">Correo no registrado. ¿Desea registrarse?</span>
          <div class="acciones">
            <a href="{{ route('registroDoc') }}" class="btn btn-primario">Si, registrame</a>
            <a href="{{ url()->previous() }}" class="btn btn-secundario">No, estoy bien</a>
          </div>
        </div>
      @endif

      <label for="password">Contraseña:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required autocomplete="new-password">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword">
      </div>

      @if(session('password_incorrecta'))
        <span class="mensaje-error">Contraseña incorrecta</span>
      @endif

      <div class="acciones">
        <a href="{{ url('/') }}" class="btn btn-secundario">Regresar</a>
        <a href="{{ route('registroDoc') }}" class="btn btn-secundario">Crear cuenta</a>
        <button type="submit" class="btn btn-primario">Iniciar sesión</button>
      </div>
    </form>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
    });
  </script>
</body>
</html>
