<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mainPac</title>
    <!-- Bootstrap CSS (única vez) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/buscar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">

</head>
<body>
    <!-- NAVBAR estilo similar a mainDoc -->
    <nav class="mainpac-nav">
        <div class="nav-left mainpac-brand">
            <img class="brand-logo" src="https://cdn0.iconfinder.com/data/icons/coronavirus-67/100/coronavirus-04-512.png" alt="Logo" />
            <span class="brand-title">MEDTECH HUB</span>
        </div>
        <div class="nav-right" style="display:flex;align-items:center;gap:12px;">
            <!-- Botón Cerrar Sesión -->
            <button type="button" id="openLogout" class="logout-btn">Cerrar sesión</button>

            <a href="{{ route('perfil.paciente') }}" style="display:inline-block;">
                @php
                    $pacienteId = session('paciente_id');
                    $ver = null;
                    if ($pacienteId) {
                        $p = \App\Models\Paciente::find($pacienteId);
                        $ver = optional($p?->updated_at)->timestamp ?? time();
                    }
                @endphp
                @if($pacienteId)
                    <img src="{{ route('avatar.paciente', $pacienteId) }}?v={{ $ver }}" alt="Perfil" style="width:40px;height:40px;border-radius:50%">
                @else
                    <img src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil" style="width:40px;height:40px;border-radius:50%">
                @endif
            </a>
        </div>
    </nav>

    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <div id="logoutModal">
        <div class="dialog">
            <p>¿Estás seguro que deseas cerrar sesión?</p>
            <div class="actions">
                <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button id="confirmLogout" type="submit" class="btn btn-primario">Sí, cerrar sesión</button>
                </form>
                <button id="cancelLogout" type="button" class="btn btn-secundario">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal confirmación eliminar consulta finalizada (solo vista del paciente) -->
    <div id="confirmDeleteModal">
        <div class="dialog">
            <div class="title">Eliminar consulta</div>
            <div class="desc">Esta acción solo la eliminará de tu vista. El doctor seguirá viéndola. ¿Deseas continuar?</div>
            <div class="actions">
                <button id="confirmDeleteBtn" type="button" class="btn btn-danger">Eliminar</button>
                <button id="cancelDeleteBtn" type="button" class="btn btn-secundario">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal confirmación finalizar consulta -->
    <div id="confirmFinalizeModal">
        <div class="dialog">
            <div class="title">Finalizar consulta</div>
            <div class="desc">Al finalizar la consulta, ya no podrás enviar mensajes en este chat. ¿Deseas continuar?</div>
            <div class="actions">
                <button id="confirmFinalizeBtn" type="button" class="btn btn-danger">Finalizar</button>
                <button id="cancelFinalizeBtn" type="button" class="btn btn-secundario">Cancelar</button>
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
        <noscript>
            @if(session('success'))
                <div class="message success">{{ session('success') }}</div>
            @endif
        </noscript>
    </div>

    <section class="container mission-vision">
        <div class="vision-card">
            <h3>Nuestra misión</h3>
            <p>
                Conectar pacientes y doctores en un entorno seguro para que las consultas se gestionen sin filas, con historial clínico disponible y una comunicación clara antes de llegar a la cita presencial.
            </p>
        </div>
        <div class="vision-card">
            <h3>Nuestra visión</h3>
            <p>
                Convertirnos en la plataforma de referencia que garantice citas agendadas, seguimiento digital y tranquilidad tanto para pacientes como para profesionales de la salud en todo el país.
            </p>
        </div>
    </section>

    <!-- Layout 3 columnas en una sola fila -->

    <!-- Contenedor unificado de 3 columnas: buscador | abiertas | finalizadas -->
    <div class="container mt-3">
        @php
            $pacienteId = session('paciente_id');
            $misActivas = collect();
            $misFinalizadas = collect();
            if ($pacienteId) {
                $hasHide = \Illuminate\Support\Facades\Schema::hasColumn('consultas','oculta_para_paciente');
                $misActivas = \App\Models\Consulta::with('doctor')
                    ->where('paciente_id', $pacienteId)
                    ->where(function($q){ $q->whereNull('status')->orWhere('status','!=','finalizado'); })
                    ->when($hasHide, fn($q) => $q->where(function($w){ $w->whereNull('oculta_para_paciente')->orWhere('oculta_para_paciente', false); }))
                    ->orderBy('created_at','desc')->get();
                $misFinalizadas = \App\Models\Consulta::with('doctor')
                    ->where('paciente_id', $pacienteId)
                    ->where('status','finalizado')
                    ->when($hasHide, fn($q) => $q->where(function($w){ $w->whereNull('oculta_para_paciente')->orWhere('oculta_para_paciente', false); }))
                    ->orderBy('created_at','desc')->get();
            }
            $activeByDoctor = $misActivas->mapWithKeys(fn($c) => [$c->doctor_id => $c->id]);
            $activeDoctorIds = $activeByDoctor->keys()->filter()->unique()->values()->all();
            $seed = null;
            if ($misActivas->count()) { $seed = $misActivas->first(); }
        @endphp
        @if($seed)
            @php
                $doc = $seed->doctor;
                $fn = $doc ? (explode(' ', trim($doc->nombre))[0] ?? $doc->nombre) : 'Doctor';
                $fl = $doc ? (explode(' ', trim($doc->apellido))[0] ?? ($doc->apellido ?? '')) : '';
                $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                $ver = optional($doc?->updated_at)->timestamp ?? time();
                $seedFoto = ($doc && $doc->id) ? route('avatar.doctor', $doc->id) . '?v=' . $ver : $defaultAvatar;
            @endphp
            <div id="pacActiveSeed" data-id="{{ $seed->id }}" data-doctor="{{ $fn }} {{ $fl }}" data-foto="{{ $seedFoto }}" data-status="{{ $seed->status ?? 'nuevo' }}" style="display:none;"></div>
        @endif

        <div class="row g-3 pac-layout">
            <!-- Columna izquierda: buscador de doctores -->
            <div class="col-lg-3 pac-left">
                <div class="card p-3">
                    <h6>Buscar doctores disponibles</h6>
                    @php
                        $q = request('q');
                        $especialidad = request('especialidad');
                        $hasDoctorEstado = \Illuminate\Support\Facades\Schema::hasColumn('doctors', 'estado');

                        $doQuery = \App\Models\Doctor::query();
                        if ($q) {
                            $doQuery->where(function($s) use ($q){ $s->where('nombre','LIKE','%'.$q.'%')->orWhere('apellido','LIKE','%'.$q.'%'); });
                        }
                        if ($especialidad) { $doQuery->where('especialidad','LIKE','%'.$especialidad.'%'); }
                        if ($hasDoctorEstado) {
                            $doQuery->where(function($w) use ($activeDoctorIds) {
                                $w->whereNull('estado')->orWhere('estado', 'activo');
                                if (!empty($activeDoctorIds)) {
                                    $w->orWhereIn('id', $activeDoctorIds);
                                }
                            });
                        }
                        $smallList = $doQuery->orderBy('nombre')->limit(12)->get();
                    @endphp
                    <form id="searchForm" onsubmit="return false;">
                        <div class="mb-2 b">
                            <input type="text" id="searchInput" name="q" class="busNom form-control form-control-sm" placeholder="Buscar por nombre" value="{{ $q }}" autocomplete="off">
                        </div>
                        <div class="mb-2">
                            <select id="specialtySelect" name="especialidad" class="busNom form-select form-select-sm">
                                <option value="">Todas las especialidades</option>
                                @foreach(['General','Cardiologo','Cirujano plastico','Pediatra','Dermatologo','Ginecologo','Neurologo','Ortopedista','Oftalmologo','Psiquiatra','Otro'] as $esp)
                                    <option value="{{ $esp }}" {{ (isset($especialidad) && $especialidad == $esp) ? 'selected' : '' }}>{{ $esp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <button id="resetFilters" class="btn btn-reset-filters">Restablecer filtros</button>
                        </div>
                    </form>
                    @if($smallList->isEmpty())
                        <div class="text-muted small">No hay doctores disponibles.</div>
                    @else
                        <ul id="doctorList" class="chat-list mb-0">
                            @foreach($smallList as $d)
                                @php
                                    $fn = explode(' ', trim($d->nombre))[0] ?? $d->nombre;
                                    $fl = explode(' ', trim($d->apellido))[0] ?? $d->apellido;
                                    $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                                    $ver = optional($d->updated_at)->timestamp ?? time();
                                    $fotoUrl = route('avatar.doctor', $d->id) . '?v=' . $ver;
                                    $existingId = $activeByDoctor[$d->id] ?? null;
                                    $estadoActual = $hasDoctorEstado ? strtolower($d->estado ?? '') : null;
                                    $forcedVisible = $hasDoctorEstado && $estadoActual === 'inactivo' && in_array($d->id, $activeDoctorIds ?? []);
                                @endphp
                                <li class="chat-item" data-id="{{ $d->id }}" data-nombre="{{ $fn }}" data-apellido="{{ $fl }}" data-especialidad="{{ $d->especialidad }}" data-descripcion="{{ e($d->descripcion) }}" data-foto="{{ $fotoUrl }}" data-estado="{{ $estadoActual ?? '' }}" data-forced="{{ $forcedVisible ? '1' : '0' }}" @if($existingId) data-existing-id="{{ $existingId }}" @endif>
                                    <div class="thumb">
                                        <img src="{{ $fotoUrl }}" alt="avatar">
                                    </div>
                                    <div class="meta">
                                    <div class="name">Dr(a). {{ $fn }} {{ $fl }}</div>
                                    <div class="sub">{{ $d->especialidad }}</div>
                                    @php $desc = $d->descripcion ? \Illuminate\Support\Str::limit($d->descripcion, 140) : null; @endphp
                                    @if($desc)
                                        <div class="desc">{{ $desc }}</div>
                                    @endif
                                    </div>
                                    <div class="actions">
                                        @if($existingId)
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="abrirChatPaciente({{ $existingId }}, {!! json_encode($fn.' '.$fl) !!}, {!! json_encode($fotoUrl) !!})">Continuar</button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openConsultaModal({{ $d->id }})">Consultar</button>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Columna centro: Consultas abiertas (chat) -->
            <div class="col-lg-5 pac-center">
                <div class="card p-3 pac-chat-card">
                    <h6>Consultas abiertas</h6>
                    <div class="detalle-consulta">
                        <div class="cabecera-consulta" id="pacChatHeader" style="display:none; align-items:center; justify-content:space-between;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img id="pacChatFoto" src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" class="avatar" alt="avatar" width="44" height="44" fetchpriority="high" decoding="async">
                                <div>
                                    <div class="name" id="pacChatTitle">Consulta</div>
                                    <div class="subtle" id="pacChatId"></div>
                                    <div class="subtle" id="pacMotivoLinea" style="margin-top:2px;"></div>
                                </div>
                            </div>
                            
                        </div>
                        <div id="pacChatEmpty" class="text-muted" style="padding:8px 4px;">No tienes consultas abiertas.</div>
                        <div class="chat-box" id="pacChatBox" style="display:none;" aria-live="polite"></div>
                        <form id="pacChatForm" class="respuesta-form" onsubmit="return enviarMensajePaciente(event)" style="display:none;">
                            <textarea id="pacChatInput" name="body" placeholder="Escribe tu mensaje..." required></textarea>
                        </form>
                        <div class="acciones-consulta" id="pacAcciones" style="margin-top:8px;"></div>
                        <div id="pacChatNote" class="subtle" style="display:none">Consulta finalizada (solo lectura)</div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Consultas finalizadas -->
            <div class="col-lg-4 pac-right">
                <div class="card p-3">
                    <h6>Consultas finalizadas</h6>
                    <ul class="chat-list" id="pacFinalizadasList">
                        @forelse($misFinalizadas as $c)
                            @php
                                $doc = $c->doctor;
                                $fn = $doc ? (explode(' ', trim($doc->nombre))[0] ?? $doc->nombre) : 'Doctor';
                                $fl = $doc ? (explode(' ', trim($doc->apellido))[0] ?? ($doc->apellido ?? '')) : '';
                                $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                                $ver = optional($doc->updated_at ?? null)->timestamp ?? time();
                                $fotoUrl = ($doc && $doc->id) ? route('avatar.doctor', $doc->id) . '?v=' . $ver : $defaultAvatar;
                                $motivo = $c->mensaje ?? '';
                            @endphp
                            <li class="chat-item" data-id="{{ $c->id }}" data-doctor="{{ $fn }} {{ $fl }}" data-foto="{{ $fotoUrl }}" data-status="finalizado">
                                <div class="thumb"><img src="{{ $fotoUrl }}" alt="avatar"></div>
                                <div class="meta">
                                    <div class="name">Dr(a). {{ $fn }} {{ $fl }}</div>
                                    <div class="sub">{{ \Illuminate\Support\Str::limit($motivo, 80) }}</div>
                                </div>
                                <div class="actions">
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-action="ocultar" data-id="{{ $c->id }}">Eliminar</button>
                                </div>
                            </li>
                        @empty
                            <li>No hay consultas finalizadas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Consulta Modal (restaurado) -->
    <div id="consultaBackdrop" class="consulta-modal-backdrop" role="dialog" aria-hidden="true" style="display:none;">
        <div class="consulta-modal" role="document">
            <div class="header">
                <div class="doctor-info">
                    <img id="modalFoto" src="{{ asset('imagenes/paciente.png') }}" alt="Avatar">
                    <div>
                        <div id="modalName" style="font-weight:700">Dr(a). Nombre Apellido</div>
                        <div id="modalEsp" style="font-size:0.95rem;color:#666">Especialidad</div>
                        <div id="modalDesc" class="desc" style="font-size:0.9rem;color:#4b5563;margin-top:4px;"></div>
                    </div>
                </div>
            </div>
            <div class="body">
                <form id="consultaForm" method="POST" action="{{ route('consultas.store') }}">
                    @csrf
                    <input type="hidden" name="doctor_id" id="modalDoctorId" value="">
                    <label for="modalMotivo" style="font-weight:600;">Motivo de la consulta</label>
                    <input id="modalMotivo" name="motivo" type="text" required maxlength="255" placeholder="Ej.: Dolor de cabeza" class="form-control" style="margin-bottom:8px;">

                    <label for="modalDescripcion" style="font-weight:600;">Descripción (se enviará al chat)</label>
                    <textarea id="modalDescripcion" name="descripcion" required placeholder="Describe los síntomas, duración, antecedentes, etc." class="form-control" style="min-height:120px;"></textarea>
                    <div class="actions">
                        <button class="btn btn-primario" type="submit">Enviar</button>
                        <button type="button" class="btn btn-secundario" onclick="closeConsultaModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="toast-container" aria-live="polite" aria-atomic="true"></div>

    <script>
        /*
         * mainPac scripts - consolidated and commented to avoid spaghetti.
         * Sections:
         *  1) Logout modal handling
         *  2) Live search (debounced) -> requests JSON from doctorController@buscar and rebuilds the left list
         *  3) Consulta modal open/close (uses data attributes from list items)
         *  4) Event wiring (input, select, reset, and delegation for list clicks)
         *  5) Toast utility (success/error) + trigger on flash messages
         *  6) Paciente chat (fetch + send)
         */

        // ---------- 1) Logout modal handling ----------
        (function(){
            const openLogout = document.getElementById('openLogout');
            const logoutModal = document.getElementById('logoutModal');
            const cancelLogout = document.getElementById('cancelLogout');
            const confirmLogout = document.getElementById('confirmLogout');
            const logoutForm = document.getElementById('logoutForm');

            if (openLogout) openLogout.addEventListener('click', () => { if (logoutModal) logoutModal.style.display = 'flex'; });
            if (cancelLogout) cancelLogout.addEventListener('click', (e) => { e.preventDefault(); if (logoutModal) logoutModal.style.display = 'none'; });
            if (confirmLogout) confirmLogout.addEventListener('click', (e) => { e.preventDefault(); if (logoutForm) logoutForm.submit(); });
        })();

        // ---------- small utilities ----------
        function debounce(fn, delay) {
            let t;
            return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
        }

        // Escape util to avoid breaking HTML when rendering messages
        function escapeHtml(text){
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text || '').replace(/[&<>"']/g, m => map[m]);
        }

        function formatTime(ts){
            // expects 'YYYY-MM-DD HH:MM:SS' -> return 'HH:MM'
            if (!ts || typeof ts !== 'string') return '';
            const parts = ts.split(' ');
            if (parts.length < 2) return '';
            return parts[1].slice(0,5);
        }

        function focusEditableTextarea(el){
            if (!el || el.disabled) return;
            requestAnimationFrame(() => {
                el.focus();
                const len = el.value ? el.value.length : 0;
                try { el.setSelectionRange(len, len); } catch (_err) {}
            });
        }

        function defaultScrollState(){ return { distance:0, atBottom:true }; }

        function captureScrollState(el){
            if (!el) return defaultScrollState();
            const distance = Math.max(0, el.scrollHeight - el.scrollTop - el.clientHeight);
            const atBottom = distance < 48;
            return { distance, atBottom };
        }

        function restoreScrollState(el, state){
            if (!el || !state) return;
            const apply = () => {
                if (state.atBottom){
                    el.scrollTop = el.scrollHeight;
                } else {
                    const target = el.scrollHeight - el.clientHeight - state.distance;
                    el.scrollTop = Math.max(0, target);
                }
            };
            apply();
            requestAnimationFrame(apply);
        }

        // ---------- 2) Live search implementation ----------
        // Build a DOM node for a doctor (keeps same structure as server-rendered items)
        const activeMap = @json($activeByDoctor);
        const activeDoctorIds = Object.keys(activeMap || {});

        function buildDoctorItem(d){
            const li = document.createElement('li');
            li.className = 'chat-item';
            li.dataset.id = d.id;
            li.dataset.nombre = (d.nombre || '').split(' ')[0] || d.nombre || '';
            li.dataset.apellido = (d.apellido || '').split(' ')[0] || d.apellido || '';
            li.dataset.especialidad = d.especialidad || '';
            li.dataset.descripcion = d.descripcion || '';
            li.dataset.foto = d.foto || '';
            if (activeMap && activeMap[d.id]) { li.dataset.existingId = activeMap[d.id]; }
            if (d.estado) { li.dataset.estado = d.estado.toLowerCase(); }
            if (d.forced) { li.dataset.forced = '1'; }

            const thumb = document.createElement('div'); thumb.className = 'thumb';
            const img = document.createElement('img'); img.src = d.foto; img.alt = 'avatar'; thumb.appendChild(img);

            const meta = document.createElement('div'); meta.className = 'meta';
            const name = document.createElement('div'); name.className = 'name'; name.textContent = 'Dr(a). ' + li.dataset.nombre + ' ' + li.dataset.apellido;
            const sub = document.createElement('div'); sub.className = 'sub'; sub.textContent = d.especialidad || '';
            const desc = document.createElement('div'); desc.className = 'desc'; desc.textContent = d.descripcion || '';
            meta.appendChild(name); meta.appendChild(sub);
            if (d.descripcion) meta.appendChild(desc);

            const actions = document.createElement('div'); actions.className = 'actions';
            const btn = document.createElement('button'); btn.type = 'button';
            const existingId = activeMap ? activeMap[d.id] : null;
            if (existingId){
                btn.className = 'btn btn-sm btn-outline-success'; btn.textContent = 'Continuar';
                const fullName = (li.dataset.nombre + ' ' + li.dataset.apellido).trim();
                btn.addEventListener('click', function(e){ e.stopPropagation(); abrirChatPaciente(existingId, fullName, d.foto); });
            } else {
                btn.className = 'btn btn-sm btn-outline-primary'; btn.textContent = 'Consultar';
                btn.addEventListener('click', function(e){ e.stopPropagation(); openConsultaModal(d.id); });
            }
            actions.appendChild(btn);

            li.appendChild(thumb); li.appendChild(meta); li.appendChild(actions);
            // clicking the item: continuar si existe, sino abrir modal
            li.addEventListener('click', function(e){
                if (e.target && e.target.tagName.toLowerCase()==='button') return;
                const fullName = (li.dataset.nombre + ' ' + li.dataset.apellido).trim();
                if (existingId){ abrirChatPaciente(existingId, fullName, d.foto); }
                else { openConsultaModal(d.id); }
            });
            return li;
        }

        // Fetch JSON results and populate the left list
        async function performLiveSearch(q, especialidad){
            const params = new URLSearchParams();
            if (q) params.set('q', q);
            if (especialidad) params.set('especialidad', especialidad);
            if (activeDoctorIds.length) {
                activeDoctorIds.forEach(id => params.append('include_active[]', id));
            }
            const url = '{{ route('buscar.doctor') }}' + (params.toString() ? ('?' + params.toString()) : '');
            try {
                const res = await fetch(url, { headers:{ 'Accept':'application/json' } });
                if (!res.ok) return;
                const json = await res.json();
                const list = document.getElementById('doctorList');
                if (!list) return;
                list.innerHTML = '';
                if (!json.data || json.data.length === 0){
                    const empty = document.createElement('div'); empty.className = 'text-muted small'; empty.textContent = 'No hay doctores disponibles.';
                    list.appendChild(empty);
                    return;
                }
                json.data.forEach(d => { list.appendChild(buildDoctorItem(d)); });
            } catch (err) {
                console.error('Live search error', err);
            }
        }

        // ---------- 3) Abrir modal para crear consulta ----------
    let pacChatCurrentId = null; // consulta id actual
    let pacChatCurrentDoctorId = null; // doctor id de la consulta actual
    let pacChatPendingDoctorId = null; // doctor seleccionado para crear consulta

        function openConsultaModal(id){
            const li = document.querySelector('.chat-item[data-id="' + id + '"]');
            const nombre = li ? ((li.dataset.nombre||'') + ' ' + (li.dataset.apellido||'')) : '';
            const foto = li ? (li.dataset.foto||'{{ asset('imagenes/paciente.png') }}') : '{{ asset('imagenes/paciente.png') }}';
            const esp = li ? (li.dataset.especialidad||'') : '';
            const desc = li ? (li.dataset.descripcion||'') : '';
            // Mostrar descripción únicamente para el doctor seleccionado
            const list = document.getElementById('doctorList');
            if (list){ list.querySelectorAll('.chat-item.show-desc').forEach(el => el.classList.remove('show-desc')); }
            if (li){ li.classList.add('show-desc'); }

            pacChatPendingDoctorId = id;
            const backdrop = document.getElementById('consultaBackdrop');
            if (!backdrop) return;
            document.getElementById('modalFoto').src = foto;
            document.getElementById('modalName').textContent = 'Dr(a). ' + nombre.trim();
            document.getElementById('modalEsp').textContent = esp;
            document.getElementById('modalDesc').textContent = desc;
            document.getElementById('modalDoctorId').value = id;
            const motivoInput = document.getElementById('modalMotivo');
            const descInput = document.getElementById('modalDescripcion');
            if (motivoInput) motivoInput.value = '';
            if (descInput) descInput.value = '';
            backdrop.style.display = 'flex';
            backdrop.setAttribute('aria-hidden','false');
        }

        function closeConsultaModal(){
            const backdrop = document.getElementById('consultaBackdrop');
            if (!backdrop) return;
            backdrop.style.display = 'none';
            backdrop.setAttribute('aria-hidden','true');
            // Al cerrar, ocultar nuevamente descripciones expandidas en la lista
            const list = document.getElementById('doctorList');
            if (list){ list.querySelectorAll('.chat-item.show-desc').forEach(el => el.classList.remove('show-desc')); }
        }

        // ---------- 5) Toasts ----------
        function showToast(message, type){
            const container = document.getElementById('toastContainer');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = 'toast ' + (type === 'error' ? 'toast-error' : 'toast-success');

            const content = document.createElement('div');
            content.className = 'toast-content';
            content.textContent = message;

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'toast-close';
            closeBtn.setAttribute('aria-label','Cerrar');
            closeBtn.innerHTML = '&times;';
            closeBtn.addEventListener('click', () => { container.removeChild(toast); });

            toast.appendChild(content);
            toast.appendChild(closeBtn);
            container.appendChild(toast);

            // animate in
            requestAnimationFrame(() => { toast.classList.add('show'); });

            // auto dismiss
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => { if (toast.parentNode) container.removeChild(toast); }, 200);
            }, 3500);
        }

        // ---------- 4) Wiring: attach handlers for inputs, select and reset button; delegate list clicks ----------
        document.addEventListener('DOMContentLoaded', function(){
            const input = document.getElementById('searchInput');
            const select = document.getElementById('specialtySelect');
            const reset = document.getElementById('resetFilters');
            const flashSuccess = @json(session('success'));
            const flashError = @json(session('error'));
            const validationErrors = @json($errors->all());

            const debounced = debounce(function(){ performLiveSearch(input.value.trim(), select.value); }, 300);

            if (input) input.addEventListener('input', debounced);
            if (select) select.addEventListener('change', debounced);

            if (reset) reset.addEventListener('click', function(e){ e.preventDefault(); input.value = ''; select.value = ''; performLiveSearch('', ''); });

            // Delegated click handler for server-rendered doctor list items (and future ones)
            const listContainer = document.getElementById('doctorList');
            if (listContainer){
                listContainer.addEventListener('click', function(e){
                    const item = e.target.closest('.chat-item');
                    if (!item) return;
                    if (e.target && e.target.tagName && e.target.tagName.toLowerCase() === 'button') return;
                    const doctorId = item.dataset.id;
                    const existingId = item.getAttribute('data-existing-id');
                    const fullName = ((item.getAttribute('data-nombre')||'') + ' ' + (item.getAttribute('data-apellido')||'')).trim();
                    const foto = item.getAttribute('data-foto');
                    if (existingId) { abrirChatPaciente(existingId, fullName, foto); }
                    else if (doctorId) { openConsultaModal(doctorId); }
                });
            }

            // Optional: trigger initial live search if there are prefilled filters
            if (input && input.value.trim() !== '' || (select && select.value !== '')){
                performLiveSearch(input.value.trim(), select.value);
            }

            // Trigger toasts from flash/validation
            if (flashSuccess) { showToast(flashSuccess, 'success'); }
            else if (flashError) { showToast(flashError, 'error'); }
            else if (validationErrors && validationErrors.length) { showToast(validationErrors[0], 'error'); }

            // Click en consultas finalizadas (lado derecho)
            const finList = document.getElementById('pacFinalizadasList');
            let pendingDeleteConsultaId = null;
            const deleteModal = document.getElementById('confirmDeleteModal');
            const deleteConfirmBtn = document.getElementById('confirmDeleteBtn');
            const deleteCancelBtn = document.getElementById('cancelDeleteBtn');

            function openDeleteModal(id){ pendingDeleteConsultaId = id; if (deleteModal) deleteModal.style.display = 'flex'; }
            function closeDeleteModal(){ if (deleteModal) deleteModal.style.display = 'none'; pendingDeleteConsultaId = null; }

            if (deleteCancelBtn) deleteCancelBtn.addEventListener('click', closeDeleteModal);
            if (deleteConfirmBtn) deleteConfirmBtn.addEventListener('click', async function(){
                const id = pendingDeleteConsultaId; if (!id) return;
                try{
                    const res = await fetch(`{{ url('/consultas') }}/${id}/ocultar-paciente`, {
                        method: 'POST', headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if (res.ok){
                        const li = document.querySelector(`#pacFinalizadasList .chat-item[data-id="${id}"]`);
                        if (li) li.remove();
                        showToast('Consulta eliminada', 'success');
                        if (String(pacChatCurrentId) === String(id)) { cerrarChatPaciente(); }
                    } else {
                        showToast('No se pudo eliminar de tu vista', 'error');
                    }
                }catch(err){ console.error(err); showToast('Error de red', 'error'); }
                closeDeleteModal();
            });

            if (finList){
                finList.addEventListener('click', async function(e){
                    // Ocultar solo para paciente
                    const btn = e.target.closest('button[data-action="ocultar"]');
                    if (btn){
                        e.stopPropagation();
                        const id = btn.getAttribute('data-id');
                        if (!id) return;
                        openDeleteModal(id);
                        return;
                    }

                    // Abrir chat al hacer click en el item (excepto botón eliminar)
                    const item = e.target.closest('.chat-item');
                    if (!item) return;
                    const id = item.getAttribute('data-id');
                    if (id) abrirChatPaciente(id);
                });
            }

            // Interceptar envío del modal para crear consulta por AJAX
            const formConsulta = document.getElementById('consultaForm');
            if (formConsulta){
                formConsulta.addEventListener('submit', async function(ev){
                    ev.preventDefault();
                    const doctorId = document.getElementById('modalDoctorId').value;
                    const motivo = (document.getElementById('modalMotivo')?.value || '').trim();
                    const descripcion = (document.getElementById('modalDescripcion')?.value || '').trim();
                    if (!doctorId || !motivo || !descripcion) return;
                    // Obtener nombre y foto del listado para pintar header al instante
                    let fullName = '';
                    let fotoUrl = '';
                    const liDoc = document.querySelector(`#doctorList .chat-item[data-id="${doctorId}"]`);
                    if (liDoc){
                        fullName = (((liDoc.dataset.nombre||'') + ' ' + (liDoc.dataset.apellido||'')).trim());
                        fotoUrl = liDoc.dataset.foto || '';
                    }
                    try{
                        const res = await fetch(`{{ route('consultas.store') }}`, {
                            method: 'POST',
                            headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ doctor_id: doctorId, motivo, descripcion })
                        });
                        if (res.status === 409){
                            const j = await res.json();
                            closeConsultaModal();
                            showToast('Ya tienes una consulta abierta con este doctor. Abriendo chat...', 'success');
                            if (j.data && j.data.id) {
                                // Preload imagen para evitar parpadeo
                                if (fotoUrl){ try{ const _img = new Image(); _img.src = fotoUrl; }catch(e){} }
                                await abrirChatPaciente(j.data.id, fullName, fotoUrl);
                            }
                            return;
                        }
                        if (!res.ok) { showToast('No se pudo crear la consulta', 'error'); return; }
                        const json = await res.json();
                        const newId = json.data && json.data.id ? json.data.id : null;
                        closeConsultaModal();
                        if (newId){
                            // Preload imagen y pintar header inmediatamente
                            if (fotoUrl){ try{ const _img = new Image(); _img.src = fotoUrl; }catch(e){} }
                            await abrirChatPaciente(newId, fullName, fotoUrl);
                            // mostrar nota de creación
                            showStatusNote('Consulta creada');
                            // actualizar mapping y botón en lista izquierda
                            if (typeof activeMap === 'object') { activeMap[doctorId] = newId; }
                            if (!activeDoctorIds.includes(String(doctorId))) {
                                activeDoctorIds.push(String(doctorId));
                            }
                            const li = document.querySelector(`#doctorList .chat-item[data-id="${doctorId}"]`);
                            if (li){
                                li.setAttribute('data-existing-id', newId);
                                const btn = li.querySelector('button');
                                if (btn){
                                    btn.textContent = 'Continuar';
                                    btn.classList.remove('btn-outline-primary');
                                    btn.classList.add('btn-outline-success');
                                    btn.onclick = function(e){ e.stopPropagation(); abrirChatPaciente(newId, fullName, fotoUrl); };
                                }
                            }
                        }
                    }catch(err){ console.error(err); showToast('Error al crear la consulta', 'error'); }
                });
            }

            // Abrir automáticamente la primera activa si existe
            const seed = document.getElementById('pacActiveSeed');
            // (default behavior) - no client-side Enter interception here; sending is handled by the form submit

            if (seed){ abrirChatPaciente(seed.getAttribute('data-id')); }

            // Enter para enviar (Shift+Enter conserva salto) en paciente
            const pacTa = document.getElementById('pacChatInput');
            if (pacTa){
                pacTa.addEventListener('keydown', function(ev){
                    if (ev.key === 'Enter' && !ev.shiftKey){
                        ev.preventDefault();
                        const f = document.getElementById('pacChatForm');
                        if (f){ (typeof f.requestSubmit === 'function') ? f.requestSubmit() : f.submit(); }
                    }
                });
            }
        });

        // ---------- 6) Paciente chat ----------
        async function abrirChatPaciente(id, doctorNombre=null, doctorFoto=null){
            pacChatCurrentId = id;
            pacChatPendingDoctorId = null;
            // header
            const item = document.querySelector(`#pacFinalizadasList .chat-item[data-id="${id}"]`) || document.getElementById('pacActiveSeed');
            const title = document.getElementById('pacChatTitle');
            const foto = document.getElementById('pacChatFoto');
            const header = document.getElementById('pacChatHeader');
            const idHolder = document.getElementById('pacChatId');
            const motivoHolder = document.getElementById('pacMotivoLinea');
            const box = document.getElementById('pacChatBox');
            const empty = document.getElementById('pacChatEmpty');
            const note = document.getElementById('pacChatNote');
            const input = document.getElementById('pacChatInput');
            const acc = document.getElementById('pacAcciones');

            const nombre = doctorNombre || (item ? (item.getAttribute('data-doctor') || 'Doctor') : 'Doctor');
            const fotoUrl = doctorFoto || (item ? (item.getAttribute('data-foto') || foto.src) : foto.src);
            let status = item ? (item.getAttribute('data-status') || 'nuevo') : 'nuevo';

            if (title) title.textContent = `Chat con Dr(a). ${nombre}`;
            if (foto) foto.src = fotoUrl;
            if (idHolder) idHolder.textContent = `ID #${id}`;
            if (header) header.style.display = '';
            if (empty) empty.style.display = 'none';
            const form = document.getElementById('pacChatForm');
            if (form) form.style.display = '';
            if (box) box.style.display = '';

            // disable/enable input by status
            const readOnly = (status === 'finalizado');
            if (input) {
                input.disabled = readOnly;
                if (!readOnly) focusEditableTextarea(input);
            }
            if (note) note.style.display = readOnly ? '' : 'none';
            if (acc) { acc.innerHTML = ''; acc.style.display = ''; }

            // fetch messages
            try{
                const res = await fetch(`{{ url('/consultas') }}/${id}/mensajes`, { headers:{ 'Accept':'application/json' } });
                const data = await res.json();
                if (box){
                    const sameChat = box.dataset.chatId === String(id);
                    const scrollState = sameChat ? captureScrollState(box) : defaultScrollState();
                    box.innerHTML = (data.data || []).map(m => `
                        <div class="bubble ${m.sender === 'doctor' ? 'from-doc' : 'from-pac'}">
                            <div class="body"><span class="text">${escapeHtml(m.body)}</span><span class="time">${formatTime(m.created_at)}</span></div>
                        </div>
                    `).join('');
                    box.dataset.chatId = String(id);
                    restoreScrollState(box, scrollState);
                }
                // Guardar doctor_id actual para poder actualizar el listado izquierdo al finalizar
                pacChatCurrentDoctorId = data.consulta && data.consulta.doctor_id ? data.consulta.doctor_id : pacChatCurrentDoctorId;
                // Usar motivo desde la consulta
                if (motivoHolder){
                    const motivoText = data.consulta && data.consulta.motivo ? data.consulta.motivo : '';
                    motivoHolder.textContent = motivoText ? ('Motivo: ' + motivoText) : '';
                }
                // Actualizar estado desde backend
                if (data.consulta && data.consulta.status) {
                    status = data.consulta.status;
                    const ro = (status === 'finalizado');
                    if (input) {
                        input.disabled = ro;
                        if (!ro) focusEditableTextarea(input);
                    }
                    if (note) note.style.display = ro ? '' : 'none';
                }
                // Botones de acciones según estado
                    if (acc && status !== 'finalizado'){
                    acc.innerHTML = `
                        <div class="acciones-row">
                            <button type="button" class="btn btn-success btn-sm" onclick="cerrarChatPaciente()" aria-label="Cerrar chat">Cerrar chat</button>
                            <button type="button" id="btnFinalizar" class="btn btn-danger btn-sm" aria-label="Finalizar consulta">Finalizar consulta</button>
                        </div>
                    `;
                    const btnFin = document.getElementById('btnFinalizar');
                    if (btnFin){ btnFin.addEventListener('click', () => openFinalizeModal(id)); }
                } else if (acc && status === 'finalizado'){
                    acc.innerHTML = `
                        <div class="acciones-row">
                            <button type="button" class="btn btn-success btn-sm" onclick="cerrarChatPaciente()" aria-label="Cerrar chat">Cerrar chat</button>
                            <span></span>
                        </div>
                    `;
                }
            }catch(e){ console.error(e); }
        }
        // Finalizar consulta: modal + AJAX
        const finalizeModal = document.getElementById('confirmFinalizeModal');
        const finalizeConfirmBtn = document.getElementById('confirmFinalizeBtn');
        const finalizeCancelBtn = document.getElementById('cancelFinalizeBtn');
        function openFinalizeModal(id){ window.__pendingFinalizeId = id; if (finalizeModal) finalizeModal.style.display = 'flex'; }
        function closeFinalizeModal(){ if (finalizeModal) finalizeModal.style.display = 'none'; window.__pendingFinalizeId = null; }
        if (finalizeCancelBtn) finalizeCancelBtn.addEventListener('click', closeFinalizeModal);
        if (finalizeConfirmBtn) finalizeConfirmBtn.addEventListener('click', async function(){
            const id = window.__pendingFinalizeId; if (!id) return;
            try{
                const res = await fetch(`{{ url('/consultas') }}/${id}/finalizar`, {
                    method: 'POST', headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (res.ok){
                    // Bloquear input y mostrar nota
                    const input = document.getElementById('pacChatInput');
                    const note = document.getElementById('pacChatNote');
                    const acc = document.getElementById('pacAcciones');
                    if (input) input.disabled = true;
                    if (note) note.style.display = '';
                    if (acc) acc.innerHTML = '';

                    // Añadir a la lista de finalizadas (derecha) si no existe
                    const finList = document.getElementById('pacFinalizadasList');
                    const exist = finList ? finList.querySelector(`.chat-item[data-id="${id}"]`) : null;
                    if (finList && !exist){
                        const title = document.getElementById('pacChatTitle');
                        const foto = document.getElementById('pacChatFoto');
                        const nombre = title ? title.textContent.replace(/^Chat con\s*/,'') : 'Doctor';
                        const fotoUrl = foto ? foto.src : 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                        const motivoLine = document.getElementById('pacMotivoLinea');
                        let motivoText = motivoLine && motivoLine.textContent ? motivoLine.textContent : '';
                        motivoText = motivoText.replace(/^\s*Motivo:\s*/i, '');
                        const li = document.createElement('li');
                        li.className = 'chat-item';
                        li.setAttribute('data-id', id);
                        li.setAttribute('data-doctor', nombre.replace(/^Chat con\s*/,''));
                        li.setAttribute('data-foto', fotoUrl);
                        li.setAttribute('data-status', 'finalizado');
                        li.innerHTML = `
                            <div class="thumb"><img src="${fotoUrl}" alt="avatar"></div>
                            <div class="meta">
                                <div class="name">${nombre}</div>
                                <div class="sub">${escapeHtml(motivoText)}</div>
                            </div>
                            <div class="actions">
                                <button type="button" class="btn btn-sm btn-outline-danger" data-action="ocultar" data-id="${id}">Eliminar</button>
                            </div>
                        `;
                        finList.prepend(li);
                    }

                    // Cerrar el chat de consultas abiertas
                    cerrarChatPaciente();

                    // Actualizar el mapeo de activos y el botón en la lista izquierda para permitir nuevas consultas
                    const doctorId = pacChatCurrentDoctorId;
                    if (doctorId){
                        if (typeof activeMap === 'object' && activeMap[doctorId]) { delete activeMap[doctorId]; }
                        const doctorIdStr = String(doctorId);
                        const idx = activeDoctorIds.indexOf(doctorIdStr);
                        if (idx !== -1) activeDoctorIds.splice(idx, 1);
                        const liDoc = document.querySelector(`#doctorList .chat-item[data-id="${doctorId}"]`);
                        if (liDoc){
                            const isForced = liDoc.getAttribute('data-forced') === '1' || liDoc.getAttribute('data-estado') === 'inactivo';
                            if (isForced){
                                liDoc.remove();
                            } else {
                                liDoc.removeAttribute('data-existing-id');
                                const btn = liDoc.querySelector('button');
                                if (btn){
                                    btn.textContent = 'Consultar';
                                    btn.classList.remove('btn-outline-success');
                                    btn.classList.add('btn-outline-primary');
                                    btn.onclick = function(e){ e.stopPropagation(); openConsultaModal(doctorId); };
                                }
                            }
                        }
                    }
                } else {
                    showToast('No se pudo finalizar', 'error');
                }
            }catch(err){ console.error(err); showToast('Error de red', 'error'); }
            closeFinalizeModal();
        });

        function cerrarChatPaciente(){
            pacChatCurrentId = null;
            const header = document.getElementById('pacChatHeader');
            const box = document.getElementById('pacChatBox');
            const form = document.getElementById('pacChatForm');
            const empty = document.getElementById('pacChatEmpty');
            const acc = document.getElementById('pacAcciones');
            const note = document.getElementById('pacChatNote');
            if (header) header.style.display = 'none';
            if (box) {
                box.style.display = 'none';
                box.innerHTML = '';
                delete box.dataset.chatId;
            }
            if (form) form.style.display = 'none';
            if (empty) empty.style.display = '';
            if (acc) { acc.innerHTML = ''; acc.style.display = 'none'; }
            if (note) note.style.display = 'none';
        }

        async function enviarMensajePaciente(e){
            e.preventDefault();
            const form = e.target;
            const textarea = form.querySelector('textarea[name="body"]');
            const body = textarea ? textarea.value.trim() : '';
            if (!body) return false;
            try{
                if (!pacChatCurrentId && pacChatPendingDoctorId){
                    // Crear consulta vía JSON con primer mensaje
                    const res = await fetch(`{{ route('consultas.store') }}`, {
                        method: 'POST',
                        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        // Si se llega aquí, no deberíamos crear desde el input; mantener compatibilidad mínima
                        body: JSON.stringify({ doctor_id: pacChatPendingDoctorId, motivo: body.slice(0,255), descripcion: body })
                    });
                    if (!res.ok) return false;
                    const json = await res.json();
                    pacChatCurrentId = json.data && json.data.id ? json.data.id : null;
                    pacChatPendingDoctorId = null;
                    if (textarea) textarea.value = '';
                    // Cargar historial desde el servidor para respetar la hora local (SV)
                    if (pacChatCurrentId) { await abrirChatPaciente(pacChatCurrentId); }
                } else if (pacChatCurrentId) {
                    const res = await fetch(`{{ url('/consultas') }}/${pacChatCurrentId}/mensajes`, {
                        method: 'POST',
                        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ body })
                    });
                    if (!res.ok) return false;
                    const rjson = await res.json();
                    // agregar burbuja sin recargar todo
                    const box = document.getElementById('pacChatBox');
                    if (box){
                        const shouldStick = captureScrollState(box).atBottom;
                        const bubble = document.createElement('div');
                        bubble.className = 'bubble from-pac';
                        const ts = rjson && rjson.data && rjson.data.created_at ? rjson.data.created_at : '';
                        bubble.innerHTML = `<div class="body"><span class="text">${escapeHtml(body)}</span><span class="time">${formatTime(ts)}</span></div>`;
                        box.appendChild(bubble);
                        if (shouldStick){
                            box.scrollTop = box.scrollHeight;
                            requestAnimationFrame(() => { box.scrollTop = box.scrollHeight; });
                        }
                    }
                    textarea.value = '';
                }
            }catch(err){ console.error(err); }
            return false;
        }

        // Mostrar una nota de estado bajo el chat (similar a finalizada) temporalmente
        function showStatusNote(text){
            const note = document.getElementById('pacChatNote');
            if (!note) return;
            note.textContent = text;
            note.style.display = '';
            // ocultar después de unos segundos si no es finalizado
            setTimeout(() => {
                // Mantener visible solo si el estado es finalizado
                const item = document.querySelector(`#pacFinalizadasList .chat-item[data-id="${pacChatCurrentId}"]`) || document.getElementById('pacActiveSeed');
                const status = item ? (item.getAttribute('data-status') || 'nuevo') : 'nuevo';
                if (status !== 'finalizado'){
                    note.style.display = 'none';
                    note.textContent = 'Consulta finalizada (solo lectura)';
                }
            }, 4000);
        }
    </script>

    <footer class="site-footer">
        <span>© 2025 MedTech Hub · Demo footer</span>
    </footer>

</body>
</html>