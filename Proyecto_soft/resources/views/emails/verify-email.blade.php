@component('mail::message')
# Hola {{ $user->name }}

Confirma tu correo para comenzar a usar MedTech HUB.

@component('mail::button', ['url' => $verificationUrl])
Confirmar correo
@endcomponent

Si el botón no funciona, copia y pega este enlace en tu navegador:
{{ $verificationUrl }}

Gracias,
El equipo de MedTech HUB
@endcomponent
