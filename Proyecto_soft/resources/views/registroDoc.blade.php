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

    <form action="{{ route('registroDoc.submit') }}" method="POST" class="formulario" autocomplete="off">
      @csrf

      <!-- DATOS PERSONALES -->
      <h2 class="subtitulo">DATOS PERSONALES</h2>

      <label for="nombre">NOMBRES:</label>
      <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>

      <label for="apellido">APELLIDOS:</label>
      <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}"  required>

      <label for="telefono">NÚMERO DE TELÉFONO:</label>
      <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Ejemplo: 1234-5678" required>

      <label for="especialidad">ESPECIALIDAD:</label>
      <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad') }}" placeholder="General" required>

      <label for="numero_colegiado">NÚMERO COLEGIADO:</label>
      <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado') }}" placeholder="123456" required>

      <label for="numero_dui">NÚMERO DE DUI:</label>
      <input type="text" id="numero_dui" name="numero_dui" value="{{ old('numero_dui') }}" placeholder="00000000-0" required style="border:2px solid #e2e8f0; padding:10px; border-radius:8px;">

      <label for="fecha_nacimiento">FECHA DE NACIMIENTO:</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;">

      <label for="sexo">SEXO:</label>
      <select id="sexo" name="sexo" required style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;">
        <option value="" disabled selected>Seleccione</option>
        <option value="Masculino" {{ old('sexo') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
        <option value="Femenino" {{ old('sexo') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
      </select>

      <label for="direccion_clinica">DIRECCIÓN DE LA CLÍNICA U HOSPITAL:</label>
      <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica') }}" placeholder="Av. Principal #123, Ciudad" required>

      <!-- CREDENCIALES -->
      <h2 class="subtitulo">CREDENCIALES</h2>

      <label for="correo">CORREO ELECTRÓNICO:</label>
      <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="ejemplo@correo.com" required>
      @error('correo')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="password">CONTRASEÑA:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required autocomplete="new-password">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword">
      </div>

      <label for="password_confirmation">CONFIRMAR CONTRASEÑA:</label>
      <div class="password-container">
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required autocomplete="new-password">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePasswordConfirm">
      </div>

      <!-- BOTONES -->
      <div class="acciones" style="gap:12px;">
        <button type="submit" class="btn btn-primario">REGISTRARSE</button>
        <a href="{{ url('/') }}" class="btn btn-secundario">REGRESAR</a>
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
