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

    <!-- MODAL DE CONFIRMACIÓN -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>¿Estás seguro que quieres cerrar sesión?</h2>
            <div class="modal-actions">
                <form id="logoutForm" action="{{ route('inicio') }}" method="POST">
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
    </script>

</body>
</html>

