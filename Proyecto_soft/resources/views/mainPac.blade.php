<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mainPac</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 16px; gap:12px;">
        <!-- Botón Cerrar Sesión -->
        <button type="button" id="openLogout" class="logout-btn">Cerrar sesión</button>

        <a href="{{ route('perfil.paciente') }}" style="display:inline-block;">
            @php
                $pacienteId = session('paciente_id');
                $foto = null;
                if ($pacienteId) {
                    $p = \App\Models\Paciente::find($pacienteId);
                    if ($p && !empty($p->foto_perfil) && file_exists(public_path('storage/profile_pics/' . $p->foto_perfil))) {
                        $foto = asset('storage/profile_pics/' . $p->foto_perfil);
                    }
                }
            @endphp
            @if($foto)
                <img src="{{ $foto }}" alt="Perfil" style="width:40px;height:40px;border-radius:50%">
            @else
                <img src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil" style="width:40px;height:40px;border-radius:50%">
            @endif
        </a>
    </div>

    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <div id="logoutModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div style="background:white; padding:20px; border-radius:8px; max-width:360px; margin:0 auto; text-align:center;">
            <p>¿Estás seguro que deseas cerrar sesión?</p>
            <div style="display:flex; gap:8px; justify-content:center; margin-top:12px;">
                <button id="confirmLogout" class="btn btn-primario">Sí, cerrar sesión</button>
                <button id="cancelLogout" class="btn btn-secundario">Cancelar</button>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        @php
            $displayName = session('paciente_nombre');
            $sexo = null;
            if (session()->has('paciente_id')) {
                $p = \App\Models\Paciente::find(session('paciente_id'));
                if ($p) { $displayName = $p->nombre . ($p->apellido ? ' ' . $p->apellido : ''); $sexo = $p->sexo ?? null; }
            } elseif (Auth::check()) {
                $u = Auth::user();
                $displayName = $u->name ?? $displayName;
            }
            $sexo = $sexo ? strtolower($sexo) : null;
            $title = 'Bienvenido';
            if ($sexo === 'femenino') { $title = 'Bienvenida'; }
        @endphp

        <h1 class="text-center mb-4">{{ $title }} {{ $displayName }}</h1>
    </div>

    <form method="GET" action="{{ route('buscar.doctor') }}" class="input-group mb-3">
        <input type="text" name="query" class="form-control" placeholder="Buscar doctor por nombre o especialidad">
        <button class="btn btn-primary" type="submit">Buscar</button>
    </form>

    <div class="row">
        @if(isset($doctores) && count($doctores) > 0)
            @foreach($doctores as $doctor)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $doctor->nombre }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $doctor->especialidad }}</h6>
                            <p class="card-text">{{ $doctor->descripcion }}</p>
                            <a href="{{ route('consulta.doctor', $doctor->id) }}" class="btn btn-success">Hacer Consulta</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-center">No se encontraron doctores.</p>
        @endif
    </div>




    <script>
        const openLogout = document.getElementById('openLogout');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');
        const logoutForm = document.getElementById('logoutForm');

        if (openLogout) openLogout.addEventListener('click', () => { if (logoutModal) logoutModal.style.display = 'flex'; });
        if (cancelLogout) cancelLogout.addEventListener('click', (e) => { e.preventDefault(); if (logoutModal) logoutModal.style.display = 'none'; });
        if (confirmLogout) confirmLogout.addEventListener('click', (e) => { e.preventDefault(); if (logoutForm) logoutForm.submit(); });
    </script>

</body>
</html>