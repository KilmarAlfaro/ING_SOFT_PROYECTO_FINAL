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
      <p>Selecciona una pregunta para ver la respuesta.</p>
    </header>

    @for($i = 1; $i <= 5; $i++)
      <article class="faq-item">
        <button class="faq-question" type="button">
          <span>Pregunta {{ $i }}</span>
          <div class="faq-icon">+</div>
        </button>
        <div class="faq-answer">
          <p>Respuesta {{ $i }}. (Contenido pendiente.)</p>
        </div>
      </article>
    @endfor

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
