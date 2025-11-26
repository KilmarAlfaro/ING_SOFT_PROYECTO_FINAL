@component('mail::message')
# Tu código de verificación

Ingresa el siguiente código de 4 dígitos para continuar con tu registro en MedTech HUB:

@component('mail::panel')
## {{ $code }}
@endcomponent

Este código expira en {{ $expirationMinutes }} minutos. Si no solicitaste este código, puedes ignorar este mensaje.

Gracias,
El equipo de MedTech HUB
@endcomponent
