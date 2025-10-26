<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Doctor</title>
    <link rel="stylesheet" href="{{ asset('css/estiloDoc.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>

    <!-- NAVBAR -->
    <nav>
        <div class="nav-left" style="display:flex;align-items:center;gap:10px;">
            <img class="brand-logo" src="https://cdn0.iconfinder.com/data/icons/coronavirus-67/100/coronavirus-04-512.png" alt="Logo" style="width:34px;height:34px;object-fit:contain;"/>
            <h1 style="margin:0;">MEDTECH HUB</h1>
        </div>

        <div class="nav-right">
            <!-- Botón Cerrar Sesión -->
            <button type="button" class="logout-btn" onclick="openModal()">Cerrar sesión</button>

            <!-- Icono Perfil -->
            <a href="{{ route('perfil.doctor') }}">
                @php
                    $doctorId = session('doctor_id');
                    $foto = null;
                    if ($doctorId) {
                        $d = \App\Models\Doctor::find($doctorId);
                        if ($d && !empty($d->foto_perfil) && file_exists(public_path('storage/profile_pics/' . $d->foto_perfil))) {
                            $foto = asset('storage/profile_pics/' . $d->foto_perfil);
                        }
                    }
                @endphp
                @if($foto)
                    <img src="{{ $foto }}" alt="Perfil" style="width:40px;height:40px;border-radius:50%">
                @else
                    <img src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil">
                @endif
            </a>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL -->
    <div id="dashboard" class="dashboard">
        <!-- Columna Izquierda: lista de consultas reales desde DB -->
        <aside class="sidebar-left">
            <h2>Consultas</h2>
            <ul class="consultas-list">
                @php
                    $doctorId = session('doctor_id');
                    $consultas = [];
                    if ($doctorId) {
                        // order by created_at ascending so the first (oldest) consulta appears at top
                        $consultas = \App\Models\Consulta::where('doctor_id', $doctorId)->orderBy('created_at', 'asc')->get();
                    }
                @endphp
                @forelse($consultas as $c)
                    <li onclick="abrirConsulta('{{ addslashes($c->mensaje) }}', {{ $c->id }})">Consulta #{{ $loop->iteration }} - {{ $c->paciente->nombre ?? 'Paciente' }}</li>
                @empty
                    <li>No tiene consultas nuevas.</li>
                @endforelse
            </ul>
        </aside>

        <!-- Columna Central -->
        <main id="mainContent" class="main-content full-width">
            <div id="contenido-principal">
                @php
                    $displayName = session('doctor_nombre');
                    $sexo = null;
                    // Prefer legacy session id
                    if (session()->has('doctor_id')) {
                        $d = \App\Models\Doctor::find(session('doctor_id'));
                        if ($d) { $displayName = $d->nombre . ($d->apellido ? ' ' . $d->apellido : ''); $sexo = $d->sexo ?? null; }
                    } elseif (Auth::check()) {
                        $d = \App\Models\Doctor::where('user_id', Auth::id())->first();
                        if ($d) { $displayName = $d->nombre . ($d->apellido ? ' ' . $d->apellido : ''); $sexo = $d->sexo ?? null; }
                    }
                    // Normalize
                    $sexo = $sexo ? strtolower($sexo) : null;
                    $title = 'Bienvenido';
                    if ($sexo === 'femenino') { $title = 'Bienvenida'; $prefix = 'Dra.'; }
                    else { $title = 'Bienvenido'; $prefix = 'Dr.'; }
                @endphp

                                <h1>{{ $title }} {{ $prefix }} {{ $displayName }}</h1>
                                <div id="consulta-detalle">
                                    <p>Seleccione una consulta en la columna izquierda para ver los detalles.</p>
                                </div>
            </div>
        </main>

        <!-- Columna Derecha (oculta al inicio) -->
        <aside id="sidebarRight" class="sidebar-right hidden">
            <h2>Comentarios</h2>
            <textarea placeholder="Escribe una nota aquí..."></textarea>
            <button class="save-btn">Guardar Nota</button>
        </aside>
    </div>

    <!-- MODAL CERRAR SESIÓN (estilo mainPac) -->
    <div id="logoutModal">
        <div class="dialog">
            <p>¿Estás seguro que deseas cerrar sesión?</p>
            <div class="actions">
                <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" id="confirmLogout" class="btn btn-primario">Sí, cerrar sesión</button>
                </form>
                <button id="cancelLogout" type="button" class="btn btn-secundario" onclick="closeModal()">Cancelar</button>
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

        function abrirConsulta(nombre, id) {
            // Mostrar detalles en el centro (populate the detalle area only)
            const detalle = document.getElementById('consulta-detalle');
            detalle.innerHTML = `
                <h2>Consulta</h2>
                <p>${nombre}</p>
                <button class="close-consulta-btn" onclick="cerrarConsulta()">Cerrar consulta</button>
            `;

            // Mostrar columna derecha
            document.getElementById("sidebarRight").classList.remove("hidden");

            // Reducir ancho del centro
            document.getElementById("mainContent").classList.remove("full-width");
        }

        function cerrarConsulta() {
            // Clear only the detalle area and restore the initial instruction text
            const detalle = document.getElementById('consulta-detalle');
            detalle.innerHTML = '<p>Seleccione una consulta en la columna izquierda para ver los detalles.</p>';

            // Ocultar columna derecha
            document.getElementById("sidebarRight").classList.add("hidden");

            // Expandir centro de nuevo
            document.getElementById("mainContent").classList.add("full-width");
        }
    </script>

</body>
</html>

