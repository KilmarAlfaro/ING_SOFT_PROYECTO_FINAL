<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio</title>
  <!-- Enlaza el CSS desde public/css/estilos.css -->
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
  <div class="inicio-contenedor">
    <h1 class="titulo">Bienvenido a <span class="empresa">MedTech HUB</span></h1>

    <div class="botones">
      <!-- Botón Soy Paciente -->
<a href="{{ route('loginPac') }}" class="card">
  <img src="https://cdn2.iconfinder.com/data/icons/virus-15/512/cough_sneeze_medical_illness_healthcare_sickness_pacient_-512.png" alt="Paciente">
  <span>Soy Paciente</span>
</a>

<!-- Botón Soy Doctor -->
<a href="{{ route('loginDoc') }}" class="card">
  <img src="https://cdn2.iconfinder.com/data/icons/covid-19-2/64/30-Doctor-1024.png" alt="Doctor">
  <span>Soy Doctor</span>
</a>

    </div>
  </div>

  <section class="valores-wrapper">
    <div class="valor-card valor-card--full" data-anim="fade-up">
        <h3>¿Quiénes somos?</h3>
        <p>
            En MedTech HUB reunimos a un equipo interdisciplinario de médicos, tecnólogos y personal de apoyo que comparte una misión: simplificar el acceso a la salud en El Salvador. Integramos herramientas digitales, trazabilidad de consultas y protocolos de seguridad para que cada paciente reciba orientación antes de llegar a la clínica y cada doctor cuente con la información necesaria para brindar un diagnóstico oportuno. Nuestro compromiso es elevar el estándar del cuidado, promover la prevención y ofrecer experiencias sin filas ni trámites innecesarios.
        </p>
    </div>
    <div class="valor-card valor-card--left" data-anim="fade-up">
      <h3>Nuestra misión</h3>
      <p>
        Facilitar la comunicación paciente-doctor desde cualquier lugar, mantener un registro claro de consultas y eliminar filas de espera gracias a citas confirmadas antes de asistir presencialmente.
      </p>
      <div class="valor-media" style="background-image:url('https://cdn-pro.elsalvador.com/wp-content/uploads/2024/07/aEDC-casos-de-dengue-en-hospitales-e-ISSS-281-1.jpg');"></div>
    </div>
    <div class="valor-card valor-card--right" data-anim="fade-up" data-delay="200">
      <h3>Nuestra visión</h3>
      <p>
        Ser la plataforma de confianza que brinde atención médica digital, segura y planificada, asegurando experiencias cómodas tanto para pacientes como para especialistas.
      </p>
      <div class="valor-media" style="background-image:url('https://www.presidencia.gob.sv/wp-content/uploads/2020/12/photo_2020-12-03_15-32-22.jpg');"></div>
    </div>
  </section>

  <footer class="inicio-footer">
    <div class="footer-logos">
      <img src="https://www.salud.gob.sv/wp-content/uploads/2020/10/banner_web_wpminsal_2020-v2-1.png" alt="Ministerio de Salud El Salvador">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQPeQbH-7Fq9vxZlvSaKhYhR_sv5fH_EDECAg&s" alt="Hospital San Francisco">
      <img src="https://medicosdeelsalvador.com/public/med/16763934273315-hospitl-especialidades.jpg" alt="Hospital de Especialidades">
    </div>
    <p>© {{ date('Y') }} MedTech HUB · Aliados estratégicos para una atención médica ágil.</p>
  </footer>

  <script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('[data-anim="fade-up"]').forEach(el => observer.observe(el));
  </script>
</body>
</html>




