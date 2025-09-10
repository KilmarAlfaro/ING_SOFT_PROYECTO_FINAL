<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro de Paciente</title>
   <link rel="stylesheet" href="{{ asset('css/repaci.css') }}">

</head>
<body>
  <main class="form-container">
    <form action="{{ route('paciente.store') }}" method="post" class="form-paciente">
      @csrf
      <h1>Registro de Paciente</h1>

      <label for="nombre">Nombre</label>
      <input type="text" id="nombre" name="nombre" placeholder="Juan" required />

      <label for="apellido">Apellido</label>
      <input type="text" id="apellido" name="apellido" placeholder="Pérez" required />

      <label for="fecha_nacimiento">Fecha de nacimiento</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required />

      <label for="sexo">Sexo</label>
      <select id="sexo" name="sexo" required>
        <option value="" disabled selected>Seleccione</option>
        <option value="Masculino">Masculino</option>
        <option value="Femenino">Femenino</option>
        <option value="Otro">Otro</option>
      </select>

      <label for="direccion">Dirección</label>
      <input type="text" id="direccion" name="direccion" placeholder="Ciudad, Calle 123" />

      <label for="telefono">Teléfono</label>
      <input type="tel" id="telefono" name="telefono" placeholder="+503 1234 5678" />

      <label for="correo">Correo electrónico</label>
      <input type="email" id="correo" name="correo" placeholder="ejemplo@mail.com" />

      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" required />

    

      <button type="submit" class="btn-submit">Registrar</button>
    </form>
  </main>
</body>
</html>
