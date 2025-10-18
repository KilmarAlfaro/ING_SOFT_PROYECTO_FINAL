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

      <label for="numero_dui">Número de DUI:</label>
      <input type="text" id="numero_dui" name="numero_dui" value="{{ old('numero_dui') }}" placeholder="00000000-0" required style="border:2px solid #e2e8f0; padding:10px; border-radius:8px;">

      <label for="fecha_nacimiento">Fecha de nacimiento:</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;">

      <label for="sexo">Sexo:</label>
      <select id="sexo" name="sexo" required style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;">
        <option value="" disabled selected>Seleccione</option>
        <option value="Masculino" {{ old('sexo') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
        <option value="Femenino" {{ old('sexo') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
      </select>

      <label for="direccion_clinica">Dirección de la clínica u hospital:</label>
      <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica') }}" placeholder="Av. Principal #123, Ciudad" required>

      <!-- CREDENCIALES -->
      <h2 class="subtitulo">Credenciales</h2>

      <label for="correo">Correo electrónico:</label>
      <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="ejemplo@correo.com" required>
      @error('correo')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="password">Contraseña:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required autocomplete="new-password">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword">
      </div>

      <label for="password_confirmation">Confirmar contraseña:</label>
      <div class="password-container">
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required autocomplete="new-password">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePasswordConfirm">
      </div>

      <!-- BOTONES -->
      <div class="acciones" style="margin-top:14px; gap:12px;">
        <button type="submit" class="btn-primario" style="background:linear-gradient(90deg,#0ea5a4,#06b6d4); padding:12px 20px; border-radius:12px; box-shadow:0 6px 18px rgba(14,165,164,0.12); color:#fff; border:none;">Registrarse</button>
        <a href="{{ url('/') }}" class="btn btn-secundario">Regresar</a>
      </div>
    </form>
  </div>

  <script>
    // Mostrar/ocultar contraseña (password y confirm)
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    function toggleFieldVisibility(field) {
      const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
      field.setAttribute('type', type);
    }

    if (togglePassword && password) togglePassword.addEventListener('click', () => toggleFieldVisibility(password));
    if (togglePasswordConfirm && passwordConfirm) togglePasswordConfirm.addEventListener('click', () => toggleFieldVisibility(passwordConfirm));
  </script>
</body>
</html>
