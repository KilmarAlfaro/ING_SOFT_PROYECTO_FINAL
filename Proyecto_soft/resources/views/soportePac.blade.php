<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ayuda para pacientes</title>
  <link rel="stylesheet" href="{{ asset('css/soporte.css') }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
  <section class="faq-wrapper">
    <header class="faq-header">
      <p class="mini">Centro de ayuda</p>
      <h1>Preguntas frecuentes de pacientes</h1>
      <p>Selecciona una pregunta para conocer cómo aprovechar tu cuenta.</p>
    </header>

    @php
      $faqs = [
        [
          'question' => '¿Cómo encuentro doctores disponibles?',
          'answer' => 'Desde tu panel usa el buscador por nombre o especialidad. Verás la descripción de cada médico y podrás iniciar una consulta en segundos.'
        ],
        [
          'question' => '¿Puedo editar mis datos personales?',
          'answer' => 'Sí. En la sección “Mi perfil” actualizas nombre, teléfono, dirección y fotografía. Los cambios quedan guardados inmediatamente.'
        ],
        [
          'question' => '¿Qué hago si un doctor no responde?',
          'answer' => 'Envía un mensaje de seguimiento. Si no recibes respuesta en un tiempo razonable puedes finalizar la consulta y buscar otro especialista activo.'
        ],
        [
          'question' => '¿Mis datos están protegidos?',
          'answer' => 'La plataforma cifra contraseñas, valida cada sesión y limita la visibilidad de tu información a los doctores con los que decides interactuar.'
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
      <p>Escríbenos a <a href="mailto:medtechsupport@gmail.com">medtechsupport@gmail.com</a></p>
    </footer>
    <div class="faq-back">
      <a href="{{ route('mainPac') }}" class="faq-back__btn">Volver a mi panel</a>
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
