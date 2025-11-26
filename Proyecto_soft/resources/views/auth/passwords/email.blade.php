<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
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
        .auth-card p {
            margin-bottom: 18px;
            text-align: center;
            color: #475569;
        }
        .auth-card label {
            font-weight: 600;
            color: #0f172a;
        }
        .auth-card input {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #cbd5f5;
            margin-top: 6px;
            margin-bottom: 14px;
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
            background: linear-gradient(120deg, #0ea5e9, #2563eb);
            color: #fff;
        }
        .link-back {
            display: inline-block;
            margin-top: 16px;
            text-decoration: none;
            color: #2563eb;
        }
        .status {
            margin-top: 14px;
            padding: 10px;
            background: rgba(34,197,94,0.12);
            border-radius: 12px;
            color: #15803d;
            text-align: center;
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
        <h1>¿Olvidaste tu contraseña?</h1>
        <p>Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" value="{{ old('email') }}" required>
            <button type="submit" class="btn-full">Enviar enlace</button>
        </form>
        <a href="{{ route(($role === 'doctor') ? 'loginDoc' : 'loginPac') }}" class="link-back">Volver al inicio de sesión</a>
        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif
    </div>
</body>
</html>
