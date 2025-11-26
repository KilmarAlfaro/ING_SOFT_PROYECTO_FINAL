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
    </style>
</head>
<body class="auth-body">
    <div class="auth-card">
        <h1>Crea una nueva contraseña</h1>
        <form action="{{ route('password.update') }}" method="POST" autocomplete="off">
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
                    minlength="6"
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
                    minlength="6"
                    autocomplete="new-password"
                    autocapitalize="none"
                    spellcheck="false"
                    data-hidden-target="passwordConfirmationHidden"
                    required>
                <button type="button" class="toggle-visibility" data-target="passwordConfirmationVisible">Ver</button>
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
    </script>
</body>
</html>
