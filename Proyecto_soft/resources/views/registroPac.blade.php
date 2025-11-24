<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro Paciente</title>
  <link rel="stylesheet" href="{{ asset('css/repaci.css') }}" />
</head>
<body>
  <div class="registro-contenedor">
  <link rel="stylesheet" href="{{ asset('css/repaci.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />

    @if ($errors->any())
      <div class="alerta-error">
        <p>Por favor corrige los siguientes campos:</p>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('registroPac.submit') }}" method="POST" autocomplete="off">
      @csrf

      <h2 class="subtitulo">Datos personales</h2>

      <label for="nombre">Nombre completo:</label>
      <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required autocomplete="off" />
      @error('nombre')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="apellido">Apellido completo:</label>
      <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" required autocomplete="off" />
      @error('apellido')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="telefono">Número de teléfono:</label>
      <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="1234-5678" inputmode="numeric" maxlength="9" data-mask="phone" required autocomplete="off" />
      @error('telefono')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="fecha_nacimiento">Fecha de nacimiento:</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required autocomplete="off" style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;" />
      @error('fecha_nacimiento')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror
      
      <label for="numero_dui">Número de DUI:</label>
      <input type="text" id="numero_dui" name="numero_dui" value="{{ old('numero_dui') }}" placeholder="00000000-0" inputmode="numeric" maxlength="10" data-mask="dui" required style="border:2px solid #e2e8f0; padding:10px; border-radius:8px;">
      @error('numero_dui')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="sexo">Sexo</label>
      <select id="sexo" name="sexo" required style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;">
        <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>Seleccione</option>
        <option value="Masculino" {{ old('sexo') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
        <option value="Femenino" {{ old('sexo') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
      </select>
      @error('sexo')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <h2 class="subtitulo">Credenciales</h2>

      <label for="correo">Correo electrónico:</label>
      <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="ejemplo@correo.com" required autocomplete="new-email" />
      @if(session('email_existente'))
        <span class="mensaje-error">Este correo ya está registrado</span>
      @endif
      @error('correo')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="password">Contraseña:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required autocomplete="new-password" />
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword" />
      </div>
      @error('password')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="password_confirmation">Confirmar contraseña:</label>
      <div class="password-container">
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required autocomplete="new-password" />
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePasswordConfirm" />
      </div>
      @error('password_confirmation')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <div class="acciones" style="gap:12px;">
        <a href="{{ url('/') }}" class="btn btn-danger">Regresar</a>
        <button type="submit" class="btn btn-primario">Registrarse</button>
      </div>
    </form>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    togglePassword.addEventListener('click', () => {
      const type = password.type === 'password' ? 'text' : 'password';
      password.type = type;
    });

    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirm = document.getElementById('password_confirmation');
    togglePasswordConfirm.addEventListener('click', () => {
      const type = passwordConfirm.type === 'password' ? 'text' : 'password';
      passwordConfirm.type = type;
    });

    const formatDui = (value) => {
      const digits = value.replace(/\D/g, '').slice(0, 9);
      return digits.length <= 8 ? digits : `${digits.slice(0, 8)}-${digits.slice(8)}`;
    };

    const formatPhone = (value) => {
      const digits = value.replace(/\D/g, '').slice(0, 8);
      return digits.length <= 4 ? digits : `${digits.slice(0, 4)}-${digits.slice(4)}`;
    };

    document.querySelectorAll('[data-mask="dui"]').forEach((input) => {
      input.addEventListener('input', () => {
        input.value = formatDui(input.value);
      });
    });

    document.querySelectorAll('[data-mask="phone"]').forEach((input) => {
      input.addEventListener('input', () => {
        input.value = formatPhone(input.value);
      });
    });
  </script>
</body>
</html>
