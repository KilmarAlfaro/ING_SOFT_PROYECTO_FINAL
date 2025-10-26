<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mainPac</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/buscar.css') }}">

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
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <h6>Buscar doctores disponibles</h6>
                @php
                    $q = request('q');
                    $especialidad = request('especialidad');

                    // prepare smallList for the left panel
                    $doQuery = \App\Models\Doctor::query();
                    if ($q) {
                        $doQuery->where(function($s) use ($q){ $s->where('nombre','LIKE','%'.$q.'%')->orWhere('apellido','LIKE','%'.$q.'%'); });
                    }
                    if ($especialidad) { $doQuery->where('especialidad','LIKE','%'.$especialidad.'%'); }
                    $smallList = $doQuery->orderBy('nombre')->limit(12)->get();
                @endphp
                <form method="GET" action="{{ route('mainPac') }}">
                    <div class="mb-2">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar por nombre" value="{{ $q }}">
                    </div>
                    <div class="mb-2">
                        <select name="especialidad" class="form-select form-select-sm">
                            <option value="">Todas las especialidades</option>
                            @foreach(['General','Cardiologo','Cirujano plastico','Pediatra','Dermatologo','Ginecologo','Neurologo','Ortopedista','Oftalmologo','Psiquiatra','Otro'] as $esp)
                                <option value="{{ $esp }}" {{ (isset($especialidad) && $especialidad == $esp) ? 'selected' : '' }}>{{ $esp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-sm btn-primary" type="submit">Filtrar</button>
                    </div>
                </form>
                @if($smallList->isEmpty())
                    <div class="text-muted small">No hay doctores disponibles.</div>
                @else
                    <ul class="chat-list mb-0" style="max-height:520px;overflow:auto;">
                        @foreach($smallList as $d)
                                @php
                                $fn = explode(' ', trim($d->nombre))[0] ?? $d->nombre;
                                $fl = explode(' ', trim($d->apellido))[0] ?? $d->apellido;
                                $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                                $fotoUrl = $d->foto_perfil ? asset('storage/profile_pics/' . $d->foto_perfil) : $defaultAvatar;
                            @endphp
                            <li class="chat-item" data-id="{{ $d->id }}" data-nombre="{{ $fn }}" data-apellido="{{ $fl }}" data-especialidad="{{ $d->especialidad }}" data-descripcion="{{ e($d->descripcion) }}" data-foto="{{ $fotoUrl }}">
                                <div class="thumb">
                                    <img src="{{ $fotoUrl }}" alt="avatar">
                                </div>
                                <div class="meta">
                                    <div class="name">Dr(a). {{ $fn }} {{ $fl }}</div>
                                    <div class="sub">{{ $d->especialidad }}</div>
                                </div>
                                <div class="actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openConsultaModal({{ $d->id }})">Consultar</button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>


                            <!-- Consulta Modal (hidden by default) -->
                            <div id="consultaBackdrop" class="consulta-modal-backdrop" role="dialog" aria-hidden="true" style="display:none;">
                                <div class="consulta-modal" role="document">
                                    <div class="header">
                                        <div class="doctor-info">
                                            <img id="modalFoto" src="{{ asset('imagenes/paciente.png') }}" alt="Avatar">
                                            <div>
                                                <div id="modalName" style="font-weight:700">Dr(a). Nombre Apellido</div>
                                                <div id="modalEsp" style="font-size:0.95rem;color:#666">Especialidad</div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="body">
                                        <p id="modalDesc" style="color:#333">Descripción</p>
                                        <form id="consultaForm" method="POST" action="{{ route('consultas.store') }}">
                                            @csrf
                                            <input type="hidden" name="doctor_id" id="modalDoctorId" value="">
                                            <label for="modalMensaje">Mensaje</label>
                                            <textarea id="modalMensaje" name="mensaje" required placeholder="Explica brevemente tu motivo y disponibilidad"></textarea>
                                            <div class="actions">
                                                <button class="btn btn-primary" type="submit">Enviar consulta</button>
                                                <button type="button" class="btn btn-closs" onclick="closeConsultaModal()">Cancelar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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

    <script>
        // Modal open/close and populate logic
        function openConsultaModal(id) {
            var li = document.querySelector('.chat-item[data-id="' + id + '"]');
            if (!li) return;
            var foto = li.dataset.foto || '{{ asset('imagenes/paciente.png') }}';
            var nombre = li.dataset.nombre || '';
            var apellido = li.dataset.apellido || '';
            var esp = li.dataset.especialidad || '';
            var desc = li.dataset.descripcion || '';

            var backdrop = document.getElementById('consultaBackdrop');
            if (!backdrop) return;
            document.getElementById('modalFoto').src = foto;
            document.getElementById('modalName').textContent = 'Dr(a). ' + nombre + ' ' + apellido;
            document.getElementById('modalEsp').textContent = esp;
            document.getElementById('modalDesc').textContent = desc || 'Sin descripción';
            document.getElementById('modalDoctorId').value = id;
            document.getElementById('modalMensaje').value = '';

            backdrop.style.display = 'flex';
            backdrop.setAttribute('aria-hidden','false');
        }

        function closeConsultaModal(){
            var backdrop = document.getElementById('consultaBackdrop');
            if (!backdrop) return;
            backdrop.style.display = 'none';
            backdrop.setAttribute('aria-hidden','true');
        }

        // allow clicking on the chat item to open modal
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('.chat-item').forEach(function(item){
                item.addEventListener('click', function(e){
                    if (e.target && e.target.tagName.toLowerCase() === 'button') return;
                    var id = this.dataset.id;
                    openConsultaModal(id);
                });
            });
        });
    </script>

</body>
</html>