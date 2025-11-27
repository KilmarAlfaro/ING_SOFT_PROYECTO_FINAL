<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Doctor</title>
    <link rel="stylesheet" href="{{ asset('css/estiloDoc.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        .dashboard-navbar {
            background: linear-gradient(120deg, #0f6fc6, #0bb4d4);
            color: #f8fafc;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 20px 35px rgba(15, 111, 198, 0.22);
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
        }
        .dashboard-navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .dashboard-navbar .navbar-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            pointer-events: none;
            user-select: none;
            transition: none;
        }
        .dashboard-navbar .navbar-logo:hover {
            transform: none;
        }
        .dashboard-navbar .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }
        .dashboard-navbar .brand-title {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .dashboard-navbar .brand-subtitle {
            font-size: 0.85rem;
            color: rgba(248, 250, 252, 0.9);
        }
        .dashboard-navbar .navbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .dashboard-navbar .help-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(248, 250, 252, 0.35);
            background: rgba(15, 23, 42, 0.08);
            color: #f8fafc;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.92rem;
            transition: background 0.2s ease;
        }
        .dashboard-navbar .help-chip:hover {
            background: rgba(15, 23, 42, 0.16);
        }
        .dashboard-navbar .help-chip .help-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(248, 250, 252, 0.2);
            font-weight: 700;
        }
        .dashboard-navbar .profile-thumb {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 2px solid rgba(248, 250, 252, 0.65);
            object-fit: cover;
        }
        @media (max-width: 768px) {
            .dashboard-navbar {
                flex-direction: column;
                gap: 14px;
            }
            .dashboard-navbar .navbar-actions {
                width: 100%;
                justify-content: flex-end;
                flex-wrap: wrap;
            }
        }
    </style>
    <!-- Chat styles moved to global CSS for consistency -->
</head>
<body class="doctor-chat">

    <!-- NAVBAR -->
    <nav class="dashboard-navbar">
        <div class="navbar-brand">
            <img class="navbar-logo" src="https://cdn0.iconfinder.com/data/icons/coronavirus-67/100/coronavirus-04-512.png" alt="Logo MedTech" />
            <div class="brand-text">
                <span class="brand-title">MedTech HUB</span>
                <small class="brand-subtitle">Panel de especialistas</small>
            </div>
        </div>

        <div class="navbar-actions">
            <a href="{{ route('soporte.doctor') }}" class="help-chip">
                <span class="help-icon">?</span>
                <span>Centro de soporte</span>
            </a>
            <button type="button" class="logout-btn" onclick="openModal()">Cerrar sesión</button>

            <!-- Icono Perfil -->
            <a href="{{ route('perfil.doctor') }}">
                @php
                    $doctorId = session('doctor_id');
                    $foto = null;
                    if ($doctorId) {
                        $d = \App\Models\Doctor::find($doctorId);
                        $ver = optional($d?->updated_at)->timestamp ?? time();
                        $foto = route('avatar.doctor', $doctorId) . '?v=' . $ver;
                    }
                @endphp
                @if($foto)
                    <img src="{{ $foto }}" alt="Perfil" class="profile-thumb">
                @else
                    <img src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil" class="profile-thumb">
                @endif
            </a>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL -->
    <div id="dashboard" class="dashboard">
        <!-- Columna Izquierda: lista de consultas activas -->
        <aside class="sidebar-left">
            <h2 style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span>Consultas</span>
                <span class="filter-wrap">
                    <input id="filtroActivas" type="text" class="list-filter" placeholder="Filtrar por paciente" style="flex:1;max-width:180px;">
                    <button type="button" class="clear-filter-btn" aria-label="Limpiar filtro" onclick="limpiarFiltro('filtroActivas')">
                        <img src="https://cdn2.iconfinder.com/data/icons/css-vol-3/24/trash-256.png" alt="Borrar" />
                    </button>
                </span>
            </h2>
            @php
                $doctorId = session('doctor_id');
                $consultasActivas = collect();
                $consultasFinalizadas = collect();
                if ($doctorId) {
                    $consultasActivas = \App\Models\Consulta::with('paciente')
                        ->where('doctor_id', $doctorId)
                        ->where(function($q){ $q->whereNull('status')->orWhere('status','!=','finalizado'); })
                        ->orderBy('created_at', 'asc')
                        ->get();
                    $consultasFinalizadas = \App\Models\Consulta::with('paciente')
                        ->where('doctor_id', $doctorId)
                        ->where('status','finalizado')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            @endphp
            <ul class="consultas-list chat-list" id="listaActivas">
                @forelse($consultasActivas as $c)
                    @php
                        $pac = $c->paciente;
                        $fn = $pac ? (explode(' ', trim($pac->nombre))[0] ?? $pac->nombre) : 'Paciente';
                        $fl = $pac ? (explode(' ', trim($pac->apellido))[0] ?? ($pac->apellido ?? '')) : '';
                        $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                        $ver = optional($pac?->updated_at)->timestamp ?? time();
                        $fotoUrl = ($pac && $pac->id) ? route('avatar.paciente', $pac->id) . '?v=' . $ver : $defaultAvatar;
                    @endphp
                    <li class="chat-item"
                        data-id="{{ $c->id }}"
                        data-paciente="{{ $fn }} {{ $fl }}"
                        data-foto="{{ $fotoUrl }}"
                        data-mensaje="{{ e($c->mensaje) }}"
                        data-respuesta="{{ e($c->respuesta) }}"
                        data-status="{{ $c->status ?? 'nuevo' }}">
                        <div class="thumb"><img src="{{ $fotoUrl }}" alt="avatar"></div>
                        <div class="meta">
                            <div class="name">{{ $fn }} {{ $fl }}</div>
                            <div class="sub">Consulta #{{ $loop->iteration }}</div>
                        </div>
                        <div class="actions">
                            <button type="button" class="btn btn-secundario" onclick="mostrarDetalle({{ $c->id }})">Ver</button>
                        </div>
                    </li>
                @empty
                    <li>No tiene consultas nuevas.</li>
                @endforelse
            </ul>
        </aside>

        <!-- Columna Central: detalle con vista tipo chat -->
        <main id="mainContent" class="main-content">
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
                <div id="consulta-detalle" class="detalle-consulta">
                    <p>Seleccione una consulta en la columna izquierda para ver los detalles.</p>
                </div>
            </div>
        </main>

        <!-- Columna Derecha: Consultas finalizadas -->
        <aside id="sidebarRight" class="sidebar-right">
            <h2 style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span>Consultas finalizadas</span>
                <span class="filter-wrap">
                    <input id="filtroFinalizadas" type="text" class="list-filter" placeholder="Filtrar por paciente" style="flex:1;max-width:180px;">
                    <button type="button" class="clear-filter-btn" aria-label="Limpiar filtro" onclick="limpiarFiltro('filtroFinalizadas')">
                        <img src="https://cdn2.iconfinder.com/data/icons/css-vol-3/24/trash-256.png" alt="Borrar" />
                    </button>
                </span>
            </h2>
            <ul class="consultas-list chat-list" id="listaFinalizadas">
                @forelse($consultasFinalizadas as $c)
                    @php
                        $pac = $c->paciente;
                        $fn = $pac ? (explode(' ', trim($pac->nombre))[0] ?? $pac->nombre) : 'Paciente';
                        $fl = $pac ? (explode(' ', trim($pac->apellido))[0] ?? ($pac->apellido ?? '')) : '';
                        $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                        $ver = optional($pac?->updated_at)->timestamp ?? time();
                        $fotoUrl = ($pac && $pac->id) ? route('avatar.paciente', $pac->id) . '?v=' . $ver : $defaultAvatar;
                    @endphp
                    <li class="chat-item" data-id="{{ $c->id }}" data-paciente="{{ $fn }} {{ $fl }}" data-foto="{{ $fotoUrl }}" data-status="finalizado">
                        <div class="thumb"><img src="{{ $fotoUrl }}" alt="avatar"></div>
                        <div class="meta">
                            <div class="name">{{ $fn }} {{ $fl }}</div>
                            <div class="sub">ID #{{ $c->id }}</div>
                        </div>
                        <div class="actions">
                            <form method="POST" action="{{ route('consultas.eliminar', $c) }}" onsubmit="event.stopPropagation();">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-secundario" onclick="openDeleteModal(this); event.stopPropagation();">Eliminar</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li>No hay consultas finalizadas.</li>
                @endforelse
            </ul>
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

    <!-- MODAL CONFIRMAR ELIMINACIÓN DE CONSULTA -->
    <div id="confirmDeleteModal">
        <div class="dialog">
            <p>¿Eliminar esta consulta definitivamente?</p>
            <div class="actions">
                <form id="deleteTargetForm" method="POST" action="#" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
                <button type="button" class="btn btn-primario" onclick="confirmDeleteSubmit()">Eliminar</button>
                <button type="button" class="btn btn-secundario" onclick="closeDeleteModal()">Cancelar</button>
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

        // ---- Delete modal helpers ----
        let deleteFormRef = null;
        function openDeleteModal(btn){
            const form = btn && btn.closest('form');
            deleteFormRef = form || null;
            document.getElementById('confirmDeleteModal').style.display = 'flex';
        }
        function closeDeleteModal(){
            document.getElementById('confirmDeleteModal').style.display = 'none';
            deleteFormRef = null;
        }
        function confirmDeleteSubmit(){
            if (deleteFormRef) deleteFormRef.submit();
            closeDeleteModal();
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

        async function mostrarDetalle(id){
            const item = document.querySelector('.chat-item[data-id="'+id+'"]');
            if (!item) return;
            const nombre = item.dataset.paciente || 'Paciente';
            const foto = item.dataset.foto || 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
            const status = item.dataset.status || 'nuevo';

            const detalle = document.getElementById('consulta-detalle');
            detalle.innerHTML = `
                <div class="cabecera-consulta" id="docChatHeader" style="display:flex;align-items:center;gap:10px;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <img id="docChatFoto" src="${foto}" alt="avatar" class="avatar" width="44" height="44">
                        <div>
                            <div class="name" id="docChatTitle">Consulta de ${nombre}</div>
                            <div class="subtle" id="docChatId">ID #${id}</div>
                            <div class="subtle" id="motivoLinea" style="margin-top:2px;"></div>
                        </div>
                    </div>
                    <span style="flex:1"></span>
                </div>
                <div id="docChatEmpty" class="text-muted" style="padding:8px 4px;display:none;">Sin mensajes todavía.</div>
                <div class="chat-box" id="chatBox" aria-live="polite"></div>
                <form id="docChatForm" class="respuesta-form" onsubmit="return enviarMensaje(event, ${id}, this)">
                    <textarea id="docChatInput" name="body" placeholder="Escribe tu respuesta..." required></textarea>
                </form>
                <div class="acciones-consulta" id="accionesConsulta"></div>
                <div id="docChatNote" class="subtle" style="display:none">Consulta finalizada (solo lectura)</div>
            `;

            // cargar mensajes
            try {
                const res = await fetch(`{{ url('/consultas') }}/${id}/mensajes`, { headers:{ 'Accept':'application/json' } });
                const data = await res.json();
                const box = document.getElementById('chatBox');
                if (box && data && Array.isArray(data.data)){
                    const sameChat = box.dataset.chatId === String(id);
                    const scrollState = sameChat ? captureScrollState(box) : defaultScrollState();
                    box.innerHTML = data.data.map(m => {
                        const t = formatTime(m.created_at);
                        const cls = m.sender === 'doctor' ? 'from-doc' : 'from-pac';
                        return `
                            <div class="bubble ${cls}">
                                <div class="body"><span class="text">${escapeHtml(m.body)}</span><span class="time">${t}</span></div>
                            </div>
                        `;
                    }).join('');
                    box.dataset.chatId = String(id);
                    restoreScrollState(box, scrollState);
                }
                // Set Motivo: prefer explicit consulta.motivo from backend; fallback to first patient message
                const motivoEl = document.getElementById('motivoLinea');
                if (motivoEl){
                    const motivoFromConsulta = data && data.consulta && data.consulta.motivo ? data.consulta.motivo : null;
                    if (motivoFromConsulta){
                        motivoEl.textContent = 'Motivo: ' + motivoFromConsulta;
                    } else if (data && Array.isArray(data.data)){
                        const firstPac = data.data.find(m => m.sender === 'paciente');
                        const firstMsg = firstPac || data.data[0];
                        motivoEl.textContent = firstMsg ? ('Motivo: ' + firstMsg.body) : '';
                    } else {
                        motivoEl.textContent = '';
                    }
                }
            } catch(e){ console.error(e); }

            // acciones (si no finalizado): form para enviar mensaje
            const acciones = document.getElementById('accionesConsulta');
            const form = document.getElementById('docChatForm');
            const input = document.getElementById('docChatInput');
            const note = document.getElementById('docChatNote');
            if (!acciones) return;
            if (status === 'finalizado'){
                if (form) form.style.display = 'none';
                if (input) input.disabled = true;
                if (note) note.style.display = '';
                acciones.innerHTML = `
                    <div class="acciones-row">
                        <button type="button" class="btn btn-success" onclick="cerrarDetalle()" aria-label="Cerrar chat">Cerrar chat</button>
                        <span></span>
                    </div>
                `;
            } else {
                if (form) form.style.display = '';
                if (input) input.disabled = false;
                if (note) note.style.display = 'none';
                acciones.innerHTML = `
                    <div class="acciones-row">
                        <button type="button" class="btn btn-success" onclick="cerrarDetalle()" aria-label="Cerrar chat">Cerrar chat</button>
                        <form method="POST" action="{{ url('/consultas') }}/${id}/finalizar" style="display:inline-flex;">
                            @csrf
                            <button type="submit" class="btn btn-danger" aria-label="Finalizar consulta">Finalizar consulta</button>
                        </form>
                    </div>
                `;
                focusEditableTextarea(input);
            }
            // Enter para enviar (Shift+Enter -> nueva línea)
            if (input){
                input.addEventListener('keydown', function(ev){
                    if (ev.key === 'Enter' && !ev.shiftKey){
                        ev.preventDefault();
                        const f = document.getElementById('docChatForm');
                        if (f){ (typeof f.requestSubmit === 'function') ? f.requestSubmit() : f.submit(); }
                    }
                });
            }
        }

        async function enviarMensaje(ev, id, form){
            if (ev && ev.preventDefault) ev.preventDefault();
            if (ev && ev.stopPropagation) ev.stopPropagation();
            const textarea = form.querySelector('textarea[name="body"]');
            const body = textarea ? textarea.value.trim() : '';
            if (!body) return false;
            try{
                const res = await fetch(`{{ url('/consultas') }}/${id}/mensajes`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ body })
                });
                if (!res.ok) return false;
                const rjson = await res.json();
                // Añadir el nuevo mensaje al chat sin reconstruir toda la vista
                const box = document.getElementById('chatBox');
                if (box){
                    const shouldStick = captureScrollState(box).atBottom;
                    const bubble = document.createElement('div');
                    bubble.className = 'bubble from-doc';
                    const t = formatTime(new Date().toISOString());
                    bubble.innerHTML = `<div class="body"><span class="text">${escapeHtml(body)}</span><span class="time">${t}</span></div>`;
                    box.appendChild(bubble);
                    if (shouldStick){
                        box.scrollTop = box.scrollHeight;
                        requestAnimationFrame(() => { box.scrollTop = box.scrollHeight; });
                    }
                }
                if (textarea) textarea.value = '';
            } catch(e){ console.error(e); }
            return false;
        }


        function escapeHtml(text){
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        function limpiarFiltro(inputId){
            const inp = document.getElementById(inputId);
            if (!inp) return;
            inp.value = '';
            // Disparar el filtrado para mostrar todos
            const evt = new Event('input', { bubbles: true });
            inp.dispatchEvent(evt);
            inp.focus();
        }

        // Formatea a HH:mm en hora local si es posible
        function formatTime(dt){
            try{
                if (!dt) return '';
                const d = new Date(dt);
                if (isNaN(d.getTime())) return '';
                const hh = String(d.getHours()).padStart(2,'0');
                const mm = String(d.getMinutes()).padStart(2,'0');
                return `${hh}:${mm}`;
            }catch(_){ return ''; }
        }

        // Delegar click en toda la fila de consulta activa
        document.addEventListener('DOMContentLoaded', function(){
            const list = document.querySelector('.sidebar-left .chat-list');
            if (!list) return;
            list.addEventListener('click', function(e){
                const item = e.target.closest('.chat-item');
                if (!item) return;
                const id = item.getAttribute('data-id');
                if (id) mostrarDetalle(id);
            });
            const finList = document.querySelector('#sidebarRight .chat-list');
            if (finList){
                finList.addEventListener('click', function(e){
                    const item = e.target.closest('.chat-item');
                    if (!item) return;
                    const id = item.getAttribute('data-id');
                    if (id) mostrarDetalle(id);
                });
            }

            // Filtros por nombre
            const filtra = (input, listUl) => {
                if (!input || !listUl) return;
                input.addEventListener('input', () => {
                    const q = input.value.trim().toLowerCase();
                    listUl.querySelectorAll('.chat-item').forEach(li => {
                        const nameEl = li.querySelector('.meta .name');
                        const name = nameEl ? nameEl.textContent.toLowerCase() : '';
                        li.style.display = name.includes(q) ? '' : 'none';
                    });
                });
            };
            filtra(document.getElementById('filtroActivas'), document.getElementById('listaActivas'));
            filtra(document.getElementById('filtroFinalizadas'), document.getElementById('listaFinalizadas'));
        });

        function cerrarDetalle(){
            const detalle = document.getElementById('consulta-detalle');
            if (!detalle) return;
            detalle.innerHTML = '<p>Seleccione una consulta en la columna izquierda para ver los detalles.</p>';
        }
    </script>

</body>
</html>

