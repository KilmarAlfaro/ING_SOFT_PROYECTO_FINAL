@component('mail::message')
# Restablece tu contraseña

Recibimos una solicitud para restablecer tu contraseña. Haz clic en el botón para continuar.

@component('mail::button', ['url' => $resetUrl])
Crear nueva contraseña
@endcomponent

Si no solicitaste este cambio, puedes ignorar este correo.

Gracias,
MedTech HUB
@endcomponent
