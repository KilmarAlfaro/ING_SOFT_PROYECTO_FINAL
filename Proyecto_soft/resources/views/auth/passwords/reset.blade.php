<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear nueva contraseña</title>
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
            width: min(420px, 90%);
            margin-inline: auto;
        }
        .auth-card h1 {
            margin-bottom: 12px;
            text-align: center;
            color: #0f172a;
        }
        .auth-card label {
            font-weight: 600;
            color: #0f172a;
            display: block;
            margin-top: 12px;
        }
        .auth-card input {
            width: 100%;
            padding: 12px 48px 12px 14px;
            border-radius: 12px;
            border: 1px solid #cbd5f5;
            margin-top: 6px;
            box-sizing: border-box;
        }
        .password-wrapper {
            position: relative;
            margin-top: 6px;
        }
        .password-wrapper input {
            padding-right: 48px;
            margin-top: 0;
        }
        .toggle-visibility {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            color: #2563eb;
            padding: 6px;
        }
        .btn-full {
            display: block;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            background: linear-gradient(120deg, #0ea5e9, #2563eb);
            color: #fff;
        }
        .error-msg {
            margin-top: 14px;
            padding: 10px;
            background: rgba(248,113,113,0.15);
            border-radius: 12px;
            color: #b91c1c;
            text-align: center;
        }
        .password-feedback {
            display: none;
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.15);
        }
        .password-feedback .feedback-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-weight: 700;
            flex-shrink: 0;
        }
        .password-feedback .feedback-text {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .password-feedback.error {
            background: linear-gradient(130deg, rgba(254, 226, 226, 0.92), rgba(254, 202, 202, 0.9));
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #b91c1c;
        }
        .password-feedback.error .feedback-icon {
            background: #fee2e2;
            color: #b91c1c;
        }
        .password-feedback.success {
            background: linear-gradient(130deg, rgba(209, 250, 229, 0.92), rgba(167, 243, 208, 0.9));
            border: 1px solid rgba(74, 222, 128, 0.5);
            color: #166534;
        }
        .password-feedback.success .feedback-icon {
            background: #dcfce7;
            color: #15803d;
        }
    </style>
</head>
<body class="auth-body">
    <div class="auth-card">
        <h1>Crea una nueva contraseña</h1>
        <form action="{{ route('password.update') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}" autocomplete="off">
            <input type="hidden" name="password" id="passwordHidden" autocomplete="new-password">
            <input type="hidden" name="password_confirmation" id="passwordConfirmationHidden" autocomplete="new-password">
            <label for="passwordVisible">Nueva contraseña</label>
            <div class="password-wrapper">
                <input type="password"
                    id="passwordVisible"
                    name="password_visible"
                    autocomplete="new-password"
                    autocapitalize="none"
                    spellcheck="false"
                    data-hidden-target="passwordHidden"
                    required>
                <button type="button" class="toggle-visibility" data-target="passwordVisible">Ver</button>
            </div>
            <label for="passwordConfirmationVisible">Confirmar nueva contraseña</label>
            <div class="password-wrapper">
                <input type="password"
                    id="passwordConfirmationVisible"
                    name="password_confirmation_visible"
                    autocomplete="new-password"
                    autocapitalize="none"
                    spellcheck="false"
                    data-hidden-target="passwordConfirmationHidden"
                    required>
                <button type="button" class="toggle-visibility" data-target="passwordConfirmationVisible">Ver</button>
            </div>
            <div class="password-feedback" id="passwordFeedback" role="alert" aria-live="polite">
                <div class="feedback-icon">!</div>
                <p class="feedback-text"></p>
            </div>
            <button type="submit" class="btn-full">Actualizar contraseña</button>
        </form>
        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif
    </div>
    <script>
        const syncHiddenFields = () => {
            document.querySelectorAll('[data-hidden-target]').forEach((input) => {
                const hidden = document.getElementById(input.dataset.hiddenTarget);
                if (hidden) {
                    hidden.value = input.value;
                }
            });
        };

        document.querySelectorAll('[data-hidden-target]').forEach((input) => {
            input.addEventListener('input', syncHiddenFields);
            input.addEventListener('change', syncHiddenFields);
        });

        syncHiddenFields();

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', () => {
                syncHiddenFields();
            });
        }

        document.querySelectorAll('.toggle-visibility').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                if (!input) {
                    return;
                }
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                btn.textContent = isPassword ? 'Ocultar' : 'Ver';
            });
        });

        const passwordField = document.getElementById('passwordVisible');
        const confirmField = document.getElementById('passwordConfirmationVisible');
        const feedbackBox = document.getElementById('passwordFeedback');
        const feedbackText = feedbackBox?.querySelector('.feedback-text');
        const feedbackIcon = feedbackBox?.querySelector('.feedback-icon');
        const messages = {
            minLength: 'Se necesita un mínimo de 6 caracteres en la contraseña.',
            mismatch: 'Las contraseñas no coinciden. Asegúrate de que ambas sean iguales.',
            match: 'Perfecto, las contraseñas coinciden.'
        };

        const showFeedback = (message, type = 'error') => {
            if (!feedbackBox || !feedbackText || !feedbackIcon) {
                return;
            }
            feedbackText.textContent = message;
            feedbackBox.style.display = 'flex';
            feedbackBox.classList.remove('error', 'success');
            feedbackBox.classList.add(type);
            feedbackIcon.textContent = type === 'success' ? '✓' : '!';
        };

        const clearFeedback = () => {
            if (!feedbackBox) {
                return;
            }
            feedbackBox.style.display = 'none';
            feedbackBox.classList.remove('error', 'success');
        };

        const validateOnConfirm = () => {
            if (!passwordField || !confirmField) {
                return;
            }
            if (passwordField.value.length < 6) {
                showFeedback(messages.minLength, 'error');
                return;
            }
            if (confirmField.value && confirmField.value !== passwordField.value) {
                showFeedback(messages.mismatch, 'error');
                return;
            }
            if (confirmField.value && confirmField.value === passwordField.value) {
                showFeedback(messages.match, 'success');
                return;
            }
            clearFeedback();
        };

        passwordField?.addEventListener('input', () => {
            if (confirmField?.value) {
                validateOnConfirm();
            } else if (passwordField.value.length < 6 && document.activeElement === confirmField) {
                showFeedback(messages.minLength, 'error');
            } else if (!confirmField?.value) {
                clearFeedback();
            }
        });

        confirmField?.addEventListener('focus', () => {
            if (!passwordField) {
                return;
            }
            if (passwordField.value.length < 6) {
                showFeedback(messages.minLength, 'error');
                return;
            }
            if (confirmField.value && confirmField.value !== passwordField.value) {
                showFeedback(messages.mismatch, 'error');
            } else if (confirmField.value && confirmField.value === passwordField.value) {
                showFeedback(messages.match, 'success');
            } else {
                clearFeedback();
            }
        });

        confirmField?.addEventListener('input', validateOnConfirm);

        form?.addEventListener('submit', (event) => {
            syncHiddenFields();
            if (!passwordField || !confirmField) {
                return;
            }
            const pwd = passwordField.value;
            const confirmation = confirmField.value;

            if (pwd.length < 6) {
                event.preventDefault();
                showFeedback(messages.minLength, 'error');
                passwordField.focus();
                return;
            }
            if (confirmation !== pwd) {
                event.preventDefault();
                showFeedback(messages.mismatch, 'error');
                confirmField.focus();
                return;
            }
            clearFeedback();
        });
    </script>
</body>
</html>
