<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Paciente</title>
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
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
    .mensaje-exito {
      color: #15803d;
      background: rgba(34,197,94,0.15);
      border-radius: 10px;
      padding: 10px;
      margin-bottom: 12px;
    }
    .forgot-link {
      text-align: right;
      margin-top: -6px;
    }
    .forgot-link a {
      color: #2563eb;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .resend-form {
      margin-top: 8px;
      display: inline-block;
    }
    .resend-form button {
      border: none;
      background: transparent;
      color: #2563eb;
      cursor: pointer;
      font-weight: 600;
    }
  </style>
</head>
<body class="auth-body">
  <div class="login-contenedor">
    <h1 class="titulo">Iniciar Sesión</h1>

    @if(session('status'))
      <div class="mensaje-exito">{{ session('status') }}</div>
    @endif

    @if(session('email_no_verificado'))
      <div class="mensaje-error">Debes verificar tu correo antes de iniciar sesión.</div>
      <form action="{{ route('verification.resend') }}" method="POST" class="resend-form">
        @csrf
        <input type="hidden" name="email" value="{{ session('pending_email') }}">
        <button type="submit">Reenviar correo de verificación</button>
      </form>
    @endif

    <form action="{{ route('loginPac.submit') }}" method="POST" class="formulario" autocomplete="off">
      @csrf

      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required autocomplete="off">

      @if(session('email_no_registrado'))
        <div class="mensaje-registro">
          <span class="mensaje-error">Correo no registrado. ¿Desea registrarse?</span>
          <div class="acciones">
            <a href="{{ route('registroPac') }}" class="btn btn-primario">Si, registrame</a>
            <a href="{{ url()->previous() }}" class="btn btn-secundario">No, estoy bien</a>
          </div>
        </div>
      @endif

      <label for="password">Contraseña:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required autocomplete="new-password">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword">
      </div>

      <div class="forgot-link">
        <a href="{{ route('password.request', ['role' => 'paciente']) }}">¿Olvidaste tu contraseña?</a>
      </div>

      @if(session('password_incorrecta'))
        <span class="mensaje-error">Contraseña incorrecta</span>
      @endif

      <div class="acciones">
        <a href="{{ url('/') }}" class="btn btn-secundario">Regresar</a>
        <a href="{{ route('registroPac') }}" class="btn btn-secundario">Crear cuenta</a>
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

