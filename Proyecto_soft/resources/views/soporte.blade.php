<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Centro de soporte</title>
  <link rel="stylesheet" href="{{ asset('css/soporte.css') }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
  <section class="faq-wrapper">
    <header class="faq-header">
      <p class="mini">Centro de ayuda</p>
      <h1>Preguntas frecuentes</h1>
    </header>

    @php
      $faqs = [
        [
          'question' => '¿En qué te puede ayudar esta página como doctor?',
          'answer' => 'Puedes crear tu perfil profesional, mostrar tus especialidades, gestionar pacientes registrados y recibir solicitudes de consultas. La plataforma centraliza tus datos para que los pacientes te encuentren con facilidad.'
        ],
        [
          'question' => '¿En qué te puede ayudar esta página como paciente?',
          'answer' => 'Te permite acceder a un directorio de doctores filtrado por especialidad. También puedes revisar la descripción de cada profesional y contactar al que mejor se adapte a tus necesidades y poder hablar con el doctor para llegar a agendar una cita y evitar las filas largas de espera.'
        ],
        [
          'question' => '¿Puedes registrarte como doctor y paciente?',
          'answer' => 'No. Los roles de doctor y paciente están separados para mantener la integridad del sistema y asegurar que cada usuario tenga acceso a las funcionalidades adecuadas según su perfil. De igual manera el sistema valida datos únicos como el correo y el DUI para evitar perfiles duplicados.'
        ],
        [
          'question' => '¿Tienes problemas en el registro?',
          'answer' => 'Verifica que tus datos coincidan con el formato solicitado (por ejemplo, el DUI sin guion) y que la contraseña tenga al menos 6 caracteres. Si persiste el error puedes escribirnos al correo: medtechsupport@gmail.com.'
        ],
        [
          'question' => '¿Tus datos están protegidos?',
          'answer' => 'Sí. El sistema encripta todo lo relacionado a datos sensibles del usuario, validamos cada registro con un código temporal asegurando la seguridad de todos tus datos.'
        ],
      ];
    @endphp

    @foreach($faqs as $faq)
      <article class="faq-item">
        <button class="faq-question" type="button">
          <span>{{ $faq['question'] }}</span>
          <div class="faq-icon">+</div>
        </button>
        <div class="faq-answer">
          <p>{{ $faq['answer'] }}</p>
        </div>
      </article>
    @endforeach

    <footer class="support-footer">
      <span>¿Necesitas más ayuda?</span>
      <p>Contáctanos en <a href="mailto:medtechsupport@gmail.com">medtechsupport@gmail.com</a></p>
    </footer>
    <div class="faq-back">
      <a href="{{ route('inicio') }}" class="faq-back__btn">Volver a inicio</a>
    </div>
  </section>

  <script>
    document.querySelectorAll('.faq-question').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.parentElement.classList.toggle('is-open');
      });
    });
  </script>
</body>
</html>
