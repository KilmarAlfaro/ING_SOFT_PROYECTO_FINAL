<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro Paciente</title>
  <link rel="stylesheet" href="{{ asset('css/repaci.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
</head>
<body>
  <div class="registro-contenedor">

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

    <div class="alerta-error" id="pacientePrevalidateErrors" style="display:none;"></div>

    <form id="pacienteRegistroForm" action="{{ route('registroPac.submit') }}" method="POST" autocomplete="off">
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

      <input type="hidden" name="otp_code" id="pacienteOtpHidden" value="">

      <div class="acciones" style="gap:12px;">
        <a href="{{ url('/') }}" class="btn btn-danger">Regresar</a>
        <button type="submit" class="btn btn-primario">Registrarse</button>
      </div>
      @error('otp_code')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror
    </form>
  </div>

  <div class="verify-overlay" id="pacienteVerifyOverlay">
    <div class="verify-modal">
      <h2>Confirma tu correo</h2>
      <p>Ingresa el código de 4 dígitos enviado a:</p>
      <span class="verify-email" id="pacienteVerifyEmail">tu correo</span>
      <div class="otp-inputs" aria-label="Código de verificación">
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" autocomplete="one-time-code" aria-label="Dígito 1" />
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" aria-label="Dígito 2" />
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" aria-label="Dígito 3" />
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" aria-label="Dígito 4" />
      </div>
      <div class="verify-actions">
        <button type="button" class="change-btn" id="pacienteChangeEmailBtn">Cambiar gmail</button>
        <button type="button" class="verify-btn" id="pacienteVerifyBtn" disabled>Verificar</button>
      </div>
      <p style="margin-top:14px; font-size:0.95rem; color:#64748b;">Verifica tu bandeja de entrada o spam si aún no ves el código.</p>
      <p class="otp-feedback" id="pacienteOtpFeedback"></p>
    </div>
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

    const otpApi = {
      prevalidate: "{{ route('registroPac.prevalidate') }}",
      validate: "{{ route('verification.otp.validate') }}",
      token: "{{ csrf_token() }}",
    };

    const collectErrorMessages = (errors) => {
      if (!errors) {
        return [];
      }
      const messages = [];
      Object.values(errors).forEach((value) => {
        if (Array.isArray(value)) {
          value.forEach((msg) => messages.push(msg));
        } else if (value) {
          messages.push(value);
        }
      });
      return messages;
    };

    const postJson = async (url, payload = {}) => {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': otpApi.token,
        },
        body: JSON.stringify(payload),
      });

      let data = {};
      try {
        data = await response.json();
      } catch (error) {
        data = {};
      }

      if (!response.ok) {
        const details = data.errors || null;
        const firstError = collectErrorMessages(details)[0];
        const err = new Error(firstError || data.message || 'Ocurrió un error inesperado.');
        err.details = details;
        throw err;
      }

      return data;
    };

    const buildPayload = (form) => {
      const formData = new FormData(form);
      formData.delete('otp_code');
      formData.delete('_token');
      return Object.fromEntries(formData.entries());
    };

    const pacienteOtpConfig = {
      form: document.getElementById('pacienteRegistroForm'),
      overlay: document.getElementById('pacienteVerifyOverlay'),
      emailInput: document.getElementById('correo'),
      emailBadge: document.getElementById('pacienteVerifyEmail'),
      otpInputs: document.querySelectorAll('#pacienteVerifyOverlay .otp-input'),
      changeButton: document.getElementById('pacienteChangeEmailBtn'),
      verifyButton: document.getElementById('pacienteVerifyBtn'),
      hiddenField: document.getElementById('pacienteOtpHidden'),
      feedbackElement: document.getElementById('pacienteOtpFeedback'),
      formErrorBox: document.getElementById('pacientePrevalidateErrors'),
    };

    const initOtpOverlay = (config) => {
      const {
        form,
        overlay,
        emailInput,
        emailBadge,
        otpInputs,
        changeButton,
        verifyButton,
        hiddenField,
        feedbackElement,
        formErrorBox,
      } = config;
      if (!form || !overlay || !otpInputs?.length || !changeButton || !verifyButton) {
        return;
      }

      let allowSubmit = false;
      let currentEmail = '';

      const renderFormErrors = (details, fallbackMessage = '') => {
        if (!formErrorBox) {
          if (fallbackMessage) {
            alert(fallbackMessage);
          }
          return;
        }
        const messages = collectErrorMessages(details);
        if (!messages.length && fallbackMessage) {
          messages.push(fallbackMessage);
        }

        if (!messages.length) {
          formErrorBox.innerHTML = '';
          formErrorBox.style.display = 'none';
          return;
        }

        const list = document.createElement('ul');
        messages.forEach((msg) => {
          const item = document.createElement('li');
          item.textContent = msg;
          list.appendChild(item);
        });
        formErrorBox.innerHTML = '';
        formErrorBox.appendChild(list);
        formErrorBox.style.display = 'block';
      };

      const clearFormErrors = () => {
        if (!formErrorBox) {
          return;
        }
        formErrorBox.innerHTML = '';
        formErrorBox.style.display = 'none';
      };

      const showFeedback = (message = '', isError = false) => {
        if (!feedbackElement) {
          return;
        }
        feedbackElement.textContent = message;
        feedbackElement.style.color = isError ? '#b91c1c' : '#15803d';
      };

      const clearOtp = () => {
        otpInputs.forEach((input) => input.value = '');
        if (hiddenField) {
          hiddenField.value = '';
        }
        verifyButton.disabled = true;
        showFeedback('');
      };

      const collectOtp = () => Array.from(otpInputs).map((input) => input.value.trim()).join('');

      const handleInput = (event, index) => {
        const onlyDigit = event.target.value.replace(/\D/g, '').slice(-1);
        event.target.value = onlyDigit;
        if (onlyDigit && index < otpInputs.length - 1) {
          otpInputs[index + 1].focus();
        }
        verifyButton.disabled = collectOtp().length !== otpInputs.length;
      };

      otpInputs.forEach((input, index) => {
        input.addEventListener('input', (event) => handleInput(event, index));
        input.addEventListener('keydown', (event) => {
          if (event.key === 'Backspace' && !input.value && index > 0) {
            otpInputs[index - 1].focus();
          }
        });
      });

      const requestPrevalidation = async () => {
        const payload = buildPayload(form);
        return postJson(otpApi.prevalidate, payload);
      };

      const validateOtpRemotely = async (email, code) => {
        showFeedback('Validando código...', false);
        await postJson(otpApi.validate, { email, code });
        showFeedback('Código validado correctamente.', false);
      };

      const openOverlay = async () => {
        const emailValue = (emailInput?.value || '').trim();
        if (!emailValue) {
          renderFormErrors(null, 'Ingresa un correo válido antes de continuar.');
          emailInput?.focus();
          return;
        }

        try {
          clearFormErrors();
          const response = await requestPrevalidation();
          currentEmail = response.email || emailValue;
          if (emailBadge) {
            emailBadge.textContent = currentEmail;
          }
          clearOtp();
          overlay.classList.add('is-visible');
          otpInputs[0]?.focus();
          showFeedback('Código enviado. Revisa tu correo.', false);
        } catch (error) {
          renderFormErrors(error.details, error.message);
        }
      };

      changeButton.addEventListener('click', () => {
        overlay.classList.remove('is-visible');
        clearOtp();
        emailInput?.focus();
      });

      verifyButton.addEventListener('click', async () => {
        const code = collectOtp();
        if (code.length !== otpInputs.length) {
          showFeedback('Ingresa los cuatro dígitos.', true);
          return;
        }
        verifyButton.disabled = true;

        try {
          await validateOtpRemotely(currentEmail, code);
          if (hiddenField) {
            hiddenField.value = code;
          }
          allowSubmit = true;
          overlay.classList.remove('is-visible');
          form.submit();
        } catch (error) {
          verifyButton.disabled = false;
          showFeedback(error.message, true);
        }
      });

      form.addEventListener('submit', async (event) => {
        if (allowSubmit) {
          return;
        }
        event.preventDefault();
        await openOverlay();
      });
    };

    initOtpOverlay(pacienteOtpConfig);
  </script>
</body>
</html>
