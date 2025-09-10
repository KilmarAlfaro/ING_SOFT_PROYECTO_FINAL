<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Doctor</title>
    <link rel="stylesheet" href="{{ asset('css/estiloDoc.css') }}">
</head>
<body>

    <!-- NAVBAR -->
    <nav>
        <h1>Panel Doctor</h1>
        <a href="{{ route('perfilDoc') }}">
            <img src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil">
        </a>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-container">
        <!-- IZQUIERDA: Lista de consultas -->
        <div class="left-panel">
            <h2>Consultas Pendientes</h2>
            <ul>
                <li class="consulta" onclick="mostrarDetalle('Consulta 1')">Consulta 1</li>
                <li class="consulta" onclick="mostrarDetalle('Consulta 2')">Consulta 2</li>
                <li class="consulta" onclick="mostrarDetalle('Consulta 3')">Consulta 3</li>
            </ul>
        </div>

        <!-- CENTRO: Detalle de la consulta -->
        <div class="center-panel">
            <h2>Detalle de la Consulta</h2>
            <div id="detalleConsulta">
                <p>Selecciona una consulta pendiente para ver sus detalles.</p>
            </div>
        </div>

        <!-- DERECHA: Notas de la consulta -->
        <div class="right-panel">
            <h2>Notas de la Consulta</h2>
            <textarea placeholder="Escribe tus notas aquí..."></textarea>
        </div>
    </div>

    <script>
        function mostrarDetalle(consulta) {
            document.getElementById('detalleConsulta').innerHTML = `
                <h3>${consulta}</h3>
                <p>Detalles completos de ${consulta} aparecerán aquí.</p>
            `;
        }
    </script>

</body>
</html>
