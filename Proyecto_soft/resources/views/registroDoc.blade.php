<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Doctor</title>
  <link rel="stylesheet" href="{{ asset('css/registroDoc.css') }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  <style>
    .alerta-form {
      border-radius: 18px;
      border: 1px solid rgba(248, 113, 113, 0.4);
      padding: 20px 24px;
      background: linear-gradient(135deg, rgba(254, 242, 242, 0.95), rgba(254, 226, 226, 0.9));
      box-shadow: 0 18px 40px rgba(248, 113, 113, 0.25);
      color: #991b1b;
      margin-bottom: 20px;
      display: flex;
      gap: 16px;
      align-items: flex-start;
    }
    .alerta-form .alerta-icon {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: #fee2e2;
      color: #b91c1c;
      display: grid;
      place-items: center;
      font-weight: 700;
      box-shadow: inset 0 0 0 1px rgba(185, 28, 28, 0.15);
      flex-shrink: 0;
      font-size: 1.1rem;
    }
    .alerta-form p {
      font-weight: 600;
      margin-bottom: 8px;
    }
    .alerta-form ul {
      margin: 0;
      padding-left: 20px;
    }
    .alerta-form li {
      margin-bottom: 4px;
    }
    .inline-error-bubble {
      margin-top: 6px;
      padding: 8px 12px;
      border-radius: 12px;
      background: rgba(254, 226, 226, 0.8);
      border: 1px solid rgba(248, 113, 113, 0.4);
      color: #b91c1c;
      font-size: 0.9rem;
      font-weight: 500;
      box-shadow: 0 10px 25px rgba(248, 113, 113, 0.2);
    }
  </style>
</head>
<body>
  <div class="registro-contenedor">
    <h1 class="titulo">Registro de Doctor</h1>

    @if ($errors->any())
      <div class="alerta-error alerta-form" id="registroDocServerErrors" tabindex="-1">
        <div class="alerta-icon" aria-hidden="true">!</div>
        <div>
          <p>Necesitamos corregir lo siguiente antes de continuar:</p>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <div class="alerta-error alerta-form" id="doctorPrevalidateErrors" style="display:none;"></div>

    <form id="doctorRegistroForm" action="{{ route('registroDoc.submit') }}" method="POST" class="formulario" autocomplete="off" novalidate>
      @csrf

      <!-- DATOS PERSONALES -->
      <h2 class="subtitulo">DATOS PERSONALES</h2>

      <label for="nombre">NOMBRES:</label>
      <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
      @error('nombre')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="apellido">APELLIDOS:</label>
      <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}"  required>
      @error('apellido')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="telefono">NÚMERO DE TELÉFONO:</label>
      <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Ejemplo: 1234-5678" inputmode="numeric" maxlength="9" data-mask="phone" required>
      @error('telefono')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="especialidad">ESPECIALIDAD:</label>
      <select id="especialidad" name="especialidad" required>
        <option value="" disabled {{ old('especialidad') ? '' : 'selected' }}>Seleccione especialidad</option>
        @php
          $especialidades = ['General','Cardiologo','Cirujano plastico','Pediatra','Dermatologo','Ginecologo','Neurologo','Ortopedista','Oftalmologo','Psiquiatra','Otro'];
        @endphp
        @foreach($especialidades as $esp)
          <option value="{{ $esp }}" {{ old('especialidad') == $esp ? 'selected' : '' }}>{{ $esp }}</option>
        @endforeach
      </select>
      <input type="text" id="especialidad_otro" name="especialidad_otro" placeholder="Especifique otra especialidad" value="{{ old('especialidad_otro') }}" style="display:none; margin-top:8px;" />
      @error('especialidad')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror
      @error('especialidad_otro')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="numero_colegiado">NÚMERO COLEGIADO:</label>
      <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado') }}" placeholder="123456" required>
      @error('numero_colegiado')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="numero_dui">NÚMERO DE DUI:</label>
      <input type="text" id="numero_dui" name="numero_dui" value="{{ old('numero_dui') }}" placeholder="00000000-0" inputmode="numeric" maxlength="10" data-mask="dui" required style="border:2px solid #e2e8f0; padding:10px; border-radius:8px;">
      @error('numero_dui')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="fecha_nacimiento">FECHA DE NACIMIENTO:</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;">
      @error('fecha_nacimiento')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="sexo">SEXO:</label>
      <select id="sexo" name="sexo" required style="padding:10px; border-radius:8px; border:2px solid #e2e8f0;">
        <option value="" disabled selected>Seleccione</option>
        <option value="Masculino" {{ old('sexo') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
        <option value="Femenino" {{ old('sexo') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
      </select>
      @error('sexo')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="direccion_clinica">DIRECCIÓN DE LA CLÍNICA U HOSPITAL:</label>
      <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica') }}" placeholder="Av. Principal #123, Ciudad" required>
      @error('direccion_clinica')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

  <label for="descripcion">BREVE DESCRIPCIÓN (Será visible en su perfil):</label>
  <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000" placeholder="Ej: Especialista en cardiología con 10 años de experiencia...">{{ old('descripcion') }}</textarea>
      @error('descripcion')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <!-- CREDENCIALES -->
      <h2 class="subtitulo">CREDENCIALES</h2>

      <label for="correo">CORREO ELECTRÓNICO:</label>
      <input type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="ejemplo@correo.com" required>
      @error('correo')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="password">CONTRASEÑA:</label>
      <div class="password-container">
        <input type="password" id="password" name="password" placeholder="********" required autocomplete="new-password" minlength="6" data-min-message="Se necesita al menos 6 caracteres en tu contraseña.">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePassword">
      </div>
      @error('password')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <label for="password_confirmation">CONFIRMAR CONTRASEÑA:</label>
      <div class="password-container">
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required autocomplete="new-password" minlength="6" data-min-message="Se necesita al menos 6 caracteres en tu contraseña." data-required-message="Repite tu contraseña para continuar.">
        <img src="https://cdn4.iconfinder.com/data/icons/ionicons/512/icon-eye-64.png" alt="Mostrar/Ocultar" id="togglePasswordConfirm">
      </div>
      @error('password_confirmation')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror

      <!-- BOTONES -->
      <input type="hidden" name="otp_code" id="doctorOtpHidden" value="">

      <div class="acciones" style="gap:12px;">
        <a href="{{ url('/') }}" class="btn btn-danger">REGRESAR</a>
        <button type="submit" class="btn btn-primario">REGISTRARSE</button>
      </div>
      @error('otp_code')
        <span class="mensaje-error">{{ $message }}</span>
      @enderror
    </form>
  </div>

  <div class="verify-overlay" id="doctorVerifyOverlay">
    <div class="verify-modal">
      <h2>Confirma tu correo</h2>
      <p>Ingresa el código de 4 dígitos enviado a:</p>
      <span class="verify-email" id="doctorVerifyEmail">tu correo</span>
      <div class="otp-inputs" aria-label="Código de verificación">
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" autocomplete="one-time-code" aria-label="Dígito 1" />
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" aria-label="Dígito 2" />
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" aria-label="Dígito 3" />
        <input type="text" maxlength="1" inputmode="numeric" class="otp-input" aria-label="Dígito 4" />
      </div>
      <div class="verify-actions">
        <button type="button" class="change-btn" id="doctorChangeEmailBtn">Cambiar gmail</button>
        <button type="button" class="verify-btn" id="doctorVerifyBtn" disabled>Verificar</button>
      </div>
      <p style="margin-top:14px; font-size:0.95rem; color:#64748b;">Revise también su carpeta de spam si no ve el código inmediatamente.</p>
      <p class="otp-feedback" id="doctorOtpFeedback"></p>
    </div>
  </div>

  <script>
    const scrollToAlert = (element) => {
      if (!element) {
        return;
      }
      if (!element.hasAttribute('tabindex')) {
        element.setAttribute('tabindex', '-1');
      }
      requestAnimationFrame(() => {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        try {
          element.focus({ preventScroll: true });
        } catch (error) {
          element.focus();
        }
      });
    };

    const serverErrorBoxDoc = document.getElementById('registroDocServerErrors');
    if (serverErrorBoxDoc) {
      setTimeout(() => scrollToAlert(serverErrorBoxDoc), 100);
    }

    const buildAlertContent = (box, messages, title = 'Revisa lo siguiente:') => {
      box.innerHTML = '';
      const icon = document.createElement('div');
      icon.className = 'alerta-icon';
      icon.textContent = '!';
      const content = document.createElement('div');
      const heading = document.createElement('p');
      heading.textContent = title;
      const list = document.createElement('ul');
      messages.forEach((msg) => {
        const item = document.createElement('li');
        item.textContent = msg;
        list.appendChild(item);
      });
      content.appendChild(heading);
      content.appendChild(list);
      box.appendChild(icon);
      box.appendChild(content);
      box.style.display = 'flex';
      scrollToAlert(box);
    };

    const initInlineValidation = (form) => {
      if (!form) {
        return;
      }

      const getBubbleAnchor = (field) => field.closest('.password-container') || field;

      const removeBubble = (field) => {
        const anchor = getBubbleAnchor(field);
        const next = anchor.nextElementSibling;
        if (next && next.classList.contains('inline-error-bubble')) {
          next.remove();
        }
      };

      const showBubble = (field, message) => {
        removeBubble(field);
        const bubble = document.createElement('div');
        bubble.className = 'inline-error-bubble';
        bubble.textContent = message;
        const anchor = getBubbleAnchor(field);
        anchor.insertAdjacentElement('afterend', bubble);
      };

      form.addEventListener('invalid', (event) => {
        event.preventDefault();
        const field = event.target;
        if (!(field instanceof HTMLElement)) {
          return;
        }
        let message = 'Completa este campo para continuar.';
        if (field.validity.valueMissing) {
          message = field.dataset.requiredMessage || message;
        } else if (field.validity.tooShort) {
          message = field.dataset.minMessage || 'Se necesita al menos 6 caracteres en tu contraseña.';
        } else if (field.validity.typeMismatch && field.type === 'email') {
          message = field.dataset.emailMessage || 'Ingresa un correo electrónico válido.';
        } else if (field.validationMessage) {
          message = field.validationMessage;
        }
        showBubble(field, message);
      }, true);

      form.addEventListener('input', (event) => {
        if (event.target instanceof HTMLElement) {
          removeBubble(event.target);
        }
      });

      form.addEventListener('focusout', (event) => {
        const field = event.target;
        if (field instanceof HTMLElement && field.checkValidity()) {
          removeBubble(field);
        }
      });
    };

    initInlineValidation(document.getElementById('doctorRegistroForm'));

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

    // Mostrar campo especificar otra especialidad
    const especialidadSelect = document.getElementById('especialidad');
    const especialidadOtro = document.getElementById('especialidad_otro');
    function toggleEspecialidadOtro() {
      if (!especialidadSelect) return;
      if (especialidadSelect.value === 'Otro') {
        especialidadOtro.style.display = 'block';
        especialidadOtro.required = true;
      } else {
        especialidadOtro.style.display = 'none';
        especialidadOtro.required = false;
        especialidadOtro.value = '';
      }
    }
    if (especialidadSelect) {
      especialidadSelect.addEventListener('change', toggleEspecialidadOtro);
      // init
      toggleEspecialidadOtro();
    }

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
      prevalidate: "{{ route('registroDoc.prevalidate') }}",
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

    const doctorOtpConfig = {
      form: document.getElementById('doctorRegistroForm'),
      overlay: document.getElementById('doctorVerifyOverlay'),
      emailInput: document.getElementById('correo'),
      emailBadge: document.getElementById('doctorVerifyEmail'),
      otpInputs: document.querySelectorAll('#doctorVerifyOverlay .otp-input'),
      changeButton: document.getElementById('doctorChangeEmailBtn'),
      verifyButton: document.getElementById('doctorVerifyBtn'),
      hiddenField: document.getElementById('doctorOtpHidden'),
      feedbackElement: document.getElementById('doctorOtpFeedback'),
      formErrorBox: document.getElementById('doctorPrevalidateErrors'),
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
        buildAlertContent(formErrorBox, messages, 'VERIFICA LOS DATOS:');
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
          const invalidField = form.querySelector(':invalid');
          if (invalidField) {
            invalidField.dispatchEvent(new Event('invalid', { bubbles: true }));
            invalidField.focus();
            return;
          }
        await openOverlay();
      });
    };

    initOtpOverlay(doctorOtpConfig);
  </script>
</body>
</html>
