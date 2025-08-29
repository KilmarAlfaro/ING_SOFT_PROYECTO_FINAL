<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio</title>
  <!-- Enlaza el CSS desde public/css/estilos.css -->
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
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
</body>
</html>




