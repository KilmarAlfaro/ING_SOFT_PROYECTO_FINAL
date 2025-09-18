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
        </div>
        <h1>Página Doctor</h1>

        <div class="nav-right">
            <!-- Botón Cerrar Sesión -->
            <button type="button" class="logout-btn" onclick="openModal()">Cerrar sesión</button>

            <!-- Icono Perfil -->
            <a href="{{ route('perfilDoc') }}">
                <img src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil">
            </a>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL -->
    <div id="dashboard" class="dashboard">
        <!-- Columna Izquierda -->
        <aside class="sidebar-left">
            <h2>Consultas</h2>
            <ul class="consultas-list">
                <li onclick="abrirConsulta('Consulta 1 - Paciente A')">Consulta #1 - Paciente A</li>
                <li onclick="abrirConsulta('Consulta 2 - Paciente B')">Consulta #2 - Paciente B</li>
                <li onclick="abrirConsulta('Consulta 3 - Paciente C')">Consulta #3 - Paciente C</li>
            </ul>
        </aside>

        <!-- Columna Central -->
        <main id="mainContent" class="main-content full-width">
            <div id="contenido-principal">
                <h1>Bienvenido Dr. Juan Pérez</h1>
                <p>Seleccione una consulta en la columna izquierda para ver los detalles.</p>
            </div>
        </main>

        <!-- Columna Derecha (oculta al inicio) -->
        <aside id="sidebarRight" class="sidebar-right hidden">
            <h2>Comentarios</h2>
            <textarea placeholder="Escribe una nota aquí..."></textarea>
            <button class="save-btn">Guardar Nota</button>
        </aside>
    </div>

    <!-- MODAL DE CONFIRMACIÓN -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>¿Estás seguro que quieres cerrar sesión?</h2>
            <div class="modal-actions">
                <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="confirm-btn">Sí</button>
                </form>
                <button type="button" class="cancel-btn" onclick="closeModal()">No</button>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("logoutModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("logoutModal").style.display = "none";
        }

        function abrirConsulta(nombre) {
            // Mostrar detalles en el centro
            document.getElementById("contenido-principal").innerHTML = `
                <h2>${nombre}</h2>
                <p>Aquí se mostrarán los detalles completos de la consulta seleccionada.</p>
                <button class="close-consulta-btn" onclick="cerrarConsulta()">Cerrar consulta</button>
            `;

            // Mostrar columna derecha
            document.getElementById("sidebarRight").classList.remove("hidden");

            // Reducir ancho del centro
            document.getElementById("mainContent").classList.remove("full-width");
        }

        function cerrarConsulta() {
            // Volver a mensaje inicial
            document.getElementById("contenido-principal").innerHTML = `
                <h1>Bienvenido Dr. Juan Pérez</h1>
                <p>Seleccione una consulta en la columna izquierda para ver los detalles.</p>
            `;

            // Ocultar columna derecha
            document.getElementById("sidebarRight").classList.add("hidden");

            // Expandir centro de nuevo
            document.getElementById("mainContent").classList.add("full-width");
        }
    </script>

</body>
</html>

