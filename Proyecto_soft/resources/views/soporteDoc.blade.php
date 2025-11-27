<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ayuda para doctores</title>
  <link rel="stylesheet" href="{{ asset('css/soporte.css') }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
  <section class="faq-wrapper">
    <header class="faq-header">
      <p class="mini">Centro de ayuda</p>
      <h1>Preguntas frecuentes de doctores</h1>
      <p>Resuelve las dudas más comunes sobre tu panel clínico.</p>
    </header>

    @php
      $faqs = [
        [
          'question' => '¿En qué te puede ayudar esta página como doctor?',
          'answer' => 'Puedes crear tu perfil profesional, mostrar tus especialidades, gestionar pacientes registrados y recibir solicitudes de consulta verificadas para mantener una agenda organizada.'
        ],
        [
          'question' => '¿Qué pasa si finalizo una consulta?',
          'answer' => 'El chat cambia a estado “finalizado”, el paciente ya no puede enviar mensajes y el historial queda disponible solo para referencia. Para continuar deberán abrir una nueva consulta.'
        ],
        [
          'question' => '¿Qué hago si el paciente dejó de responder?',
          'answer' => 'Envía un mensaje de seguimiento. Si no hay respuesta puedes finalizar la consulta para liberar espacio.'
        ],
        [
          'question' => '¿Cómo puedo actualizar mis datos?',
          'answer' => 'Desde tu perfil editas teléfono, especialidad, descripción y foto. Guarda los cambios y se actualizaran inmediatamente.'
        ],
        [
          'question' => '¿Hay alguna manera de ya no recibir consultas nuevas?',
          'answer' => 'Si, cambia tu estado a “inactivo” en el perfil. Mantendrás el acceso al historial, pero dejarás de aparecer en la búsqueda hasta que lo actives de nuevo.'
        ],
        [
          'question' => '¿Qué pasa si elimino mi cuenta?',
          'answer' => 'Despublicamos tu perfil y se revoca tu acceso y ningún paciente podrá crear nuevas conversaciones contigo.'
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
      <a href="{{ route('mainDoc') }}" class="faq-back__btn">Volver a mi panel</a>
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
