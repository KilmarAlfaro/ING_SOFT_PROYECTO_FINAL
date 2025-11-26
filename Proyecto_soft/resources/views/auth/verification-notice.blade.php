<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo</title>
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        .verification-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 36px;
            border-radius: 22px;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
            max-width: 440px;
            width: 90%;
            text-align: center;
        }
        .verification-card h1 {
            margin-bottom: 12px;
            color: #0f172a;
        }
        .verification-card p {
            color: #475569;
            line-height: 1.6;
        }
        .verification-card .correo {
            display: inline-block;
            margin: 12px 0;
            padding: 10px 18px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.2);
            color: #0f172a;
            font-weight: 600;
        }
        .spinner {
            width: 42px;
            height: 42px;
            border: 4px solid rgba(14,165,233,0.2);
            border-top-color: #0ea5e9;
            border-radius: 50%;
            margin: 0 auto 18px;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .btn-block {
            display: block;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
        }
        .btn-primary {
            background: linear-gradient(120deg, #0ea5e9, #2563eb);
            color: #fff;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }
        .status {
            background: rgba(34,197,94,0.15);
            color: #15803d;
            border-radius: 12px;
            padding: 10px;
            margin-top: 12px;
            font-size: 0.95rem;
        }
        .error-msg {
            background: rgba(248,113,113,0.18);
            color: #b91c1c;
            border-radius: 12px;
            padding: 10px;
            margin-top: 12px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body class="auth-body">
    <div class="verification-card">
        <div class="spinner"></div>
        <h1>Se necesita una verificación</h1>
        <p>Enviamos un correo de confirmación a:</p>
        <span class="correo">{{ $email }}</span>
        <p>Abre el mensaje y haz clic en el enlace para activar tu cuenta. Una vez verificado, podrás iniciar sesión.</p>

        <form action="{{ route('verification.resend') }}" method="POST" style="margin-top:18px;">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="btn-block btn-primary">Reenviar correo</button>
        </form>

        <a href="{{ route($role === 'doctor' ? 'loginDoc' : 'loginPac') }}" class="btn-block btn-secondary" style="text-decoration:none;">Ir al inicio de sesión</a>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif
    </div>
</body>
</html>
