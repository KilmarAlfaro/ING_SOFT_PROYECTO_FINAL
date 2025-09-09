<!-- registro_paciente.html -->
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro de Paciente</title>
</head>
<body>
<h1>Registro de Paciente</h1>
<form action="procesar_registro.php" method="post">
  <label for="nombre">Nombre completo:</label><br>
  <input type="text" id="nombre" name="nombre" required><br><br>
  
  <label for="fecha_nac">Fecha de nacimiento:</label><br>
  <input type="date" id="fecha_nac" name="fecha_nac" required><br><br>

  <label for="sexo">Sexo:</label><br>
  <select id="sexo" name="sexo" required>
    <option value="">Seleccione...</option>
    <option value="Masculino">Masculino</option>
    <option value="Femenino">Femenino</option>
    <option value="Otro">Otro</option>
  </select><br><br>

  <label for="direccion">Dirección:</label><br>
  <input type="text" id="direccion" name="direccion"><br><br>

  <label for="telefono">Teléfono:</label><br>
  <input type="tel" id="telefono" name="telefono"><br><br>

  <label for="email">Correo electrónico:</label><br>
  <input type="email" id="email" name="email"><br><br>
  
  <label for="alergias">Alergias:</label><br>
  <input type="text" id="alergias" name="alergias"><br><br>

  <label for="medicamentos">Medicamentos actuales:</label><br>
  <input type="text" id="medicamentos" name="medicamentos"><br><br>

  <input type="submit" value="Registrar">
</form>
</body>
</html>
