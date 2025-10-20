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

    <div class="row">
        <div class="col-md-3">
            <div class="card p-3 mb-3">
                <h6>Buscar</h6>
                @php
                    $q = request('q');
                    $especialidad = request('especialidad');
                @endphp
                <form method="GET" action="{{ route('mainPac') }}">
                    <div class="mb-2">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Nombre" value="{{ $q }}">
                    </div>
                    <div class="mb-2">
                        <select name="especialidad" class="form-select form-select-sm">
                            <option value="">Todas las especialidades</option>
                            @php
                                $especialidades = ['General','Cardiologo','Cirujano plastico','Pediatra','Dermatologo','Ginecologo','Neurologo','Ortopedista','Oftalmologo','Psiquiatra','Otro'];
                            @endphp
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp }}" {{ (isset($especialidad) && $especialidad == $esp) ? 'selected' : '' }}>{{ $esp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-sm btn-primary" type="submit">Filtrar</button>
                    </div>
                </form>
            </div>

            <div class="card p-2">
                <h6 class="mb-2">Doctores</h6>
                @php
                    $doQuery = \App\Models\Doctor::query();
                    if ($q) {
                        $doQuery->where(function($s) use ($q){ $s->where('nombre','LIKE','%'.$q.'%')->orWhere('apellido','LIKE','%'.$q.'%'); });
                    }
                    if ($especialidad) { $doQuery->where('especialidad','LIKE','%'.$especialidad.'%'); }
                    $smallList = $doQuery->orderBy('nombre')->limit(8)->get();
                @endphp
                @if($smallList->isEmpty())
                    <div class="text-muted small">No hay doctores.</div>
                @else
                    <ul class="list-unstyled mb-0">
                        @foreach($smallList as $d)
                            @php $fn = explode(' ', trim($d->nombre))[0] ?? $d->nombre; $fl = explode(' ', trim($d->apellido))[0] ?? $d->apellido; @endphp
                            <li class="d-flex align-items-center py-2 border-bottom">
                                <div style="width:36px;height:36px;margin-right:8px;">
                                    @if($d->foto_perfil)
                                        <img src="{{ asset('storage/profile_pics/' . $d->foto_perfil) }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                    @else
                                        <img src="{{ asset('imagenes/paciente.png') }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                    @endif
                                </div>
                                <div style="flex:1;">
                                    <div style="font-size:0.95rem;font-weight:600;">Dr(a). {{ $fn }} {{ $fl }}</div>
                                    <div style="font-size:0.8rem;color:#666;">{{ $d->especialidad }}</div>
                                </div>
                                <div>
                                    <a href="{{ route('consulta.doctor', $d->id) }}" class="btn btn-sm btn-outline-primary">Consultar</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-md-9">
            @php
                // load full set for right area if needed
                $rightQuery = \App\Models\Doctor::query();
                if ($q) { $rightQuery->where(function($s) use ($q){ $s->where('nombre','LIKE','%'.$q.'%')->orWhere('apellido','LIKE','%'.$q.'%'); }); }
                if ($especialidad) { $rightQuery->where('especialidad','LIKE','%'.$especialidad.'%'); }
                $doctores = $rightQuery->orderBy('nombre')->paginate(12);
            @endphp

            <div class="row">
                @forelse($doctores as $doctor)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $doctor->nombre }} {{ $doctor->apellido }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">{{ $doctor->especialidad }}</h6>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($doctor->descripcion ?? 'Sin descripción', 200) }}</p>
                                <a href="{{ route('consulta.doctor', $doctor->id) }}" class="btn btn-success">Hacer Consulta</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><p class="text-center">No se encontraron doctores.</p></div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center">{{ $doctores->links() }}</div>
        </div>
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