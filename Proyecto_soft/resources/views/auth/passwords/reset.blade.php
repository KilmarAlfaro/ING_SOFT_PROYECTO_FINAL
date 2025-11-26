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
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #cbd5f5;
            margin-top: 6px;
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
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <label for="password">Nueva contraseña</label>
            <input type="password" id="password" name="password" required>
            <label for="password_confirmation">Confirmar nueva contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            <button type="submit" class="btn-full">Actualizar contraseña</button>
        </form>
        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif
    </div>
</body>
</html>
