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
                    $foto = $doctorId ? route('avatar.doctor', $doctorId) : null;
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
                        $fotoUrl = ($pac && $pac->id) ? route('avatar.paciente', $pac->id) : $defaultAvatar;
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
                        $fotoUrl = ($pac && $pac->id) ? route('avatar.paciente', $pac->id) : $defaultAvatar;
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

        async function mostrarDetalle(id){
            const item = document.querySelector('.chat-item[data-id="'+id+'"]');
            if (!item) return;
            const nombre = item.dataset.paciente || 'Paciente';
            const foto = item.dataset.foto || 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
            const status = item.dataset.status || 'nuevo';

            const detalle = document.getElementById('consulta-detalle');
            detalle.innerHTML = `
                <div class="cabecera-consulta">
                    <img src="${foto}" alt="avatar" class="avatar">
                    <div>
                        <h2 style="margin:0">Consulta de ${nombre}</h2>
                        <div id="motivoLinea" class="subtle"></div>
                    </div>
                    <span style="flex:1"></span>
                </div>
                <div class="chat-box" id="chatBox"></div>
                <div class="acciones-consulta" id="accionesConsulta"></div>
            `;

            // cargar mensajes
            try {
                const res = await fetch(`{{ url('/consultas') }}/${id}/mensajes`, { headers:{ 'Accept':'application/json' } });
                const data = await res.json();
                const box = document.getElementById('chatBox');
                if (box && data && Array.isArray(data.data)){
                    box.innerHTML = data.data.map(m => {
                        const t = formatTime(m.created_at);
                        const cls = m.sender === 'doctor' ? 'from-doc' : 'from-pac';
                        return `
                            <div class="bubble ${cls}">
                                <div class="body">${escapeHtml(m.body)}</div>
                                <div class="meta time">${t}</div>
                            </div>
                        `;
                    }).join('');
                    box.scrollTop = box.scrollHeight;
                }
                // Set Motivo (primer mensaje del paciente o el primero en general)
                const motivoEl = document.getElementById('motivoLinea');
                if (motivoEl && data && Array.isArray(data.data)){
                    const firstPac = data.data.find(m => m.sender === 'paciente');
                    const firstMsg = firstPac || data.data[0];
                    motivoEl.textContent = firstMsg ? ('Motivo: ' + firstMsg.body) : '';
                }
            } catch(e){ console.error(e); }

            // acciones (si no finalizado): form para enviar mensaje
            const acciones = document.getElementById('accionesConsulta');
            if (!acciones) return;
            if (status !== 'finalizado'){
                acciones.innerHTML = `
                    <form class="respuesta-form" onsubmit="return enviarMensaje(event, ${id}, this)">
                        <textarea name="body" placeholder="Escribe tu respuesta..." required></textarea>
                        <button type="submit" class="send-btn" aria-label="Enviar">
                            <img src="https://cdn0.iconfinder.com/data/icons/zondicons/20/send-256.png" alt="Enviar" width="20" height="20"/>
                        </button>
                    </form>
                    <div class="acciones-row">
                        <button type="button" class="btn btn-success" onclick="cerrarDetalle()">Cerrar chat</button>
                        <form method="POST" action="{{ url('/consultas') }}/${id}/finalizar">
                            @csrf
                            <button type="submit" class="btn btn-danger">Finalizar consulta</button>
                        </form>
                    </div>
                `;
            } else {
                acciones.innerHTML = `
                    <div class="acciones-row">
                        <button type="button" class="btn btn-success" onclick="cerrarDetalle()">Cerrar chat</button>
                        <span></span>
                    </div>
                `;
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
                    const bubble = document.createElement('div');
                    bubble.className = 'bubble from-doc';
                    const t = formatTime(new Date().toISOString());
                    bubble.innerHTML = `
                        <div class="body"></div>
                        <div class="meta time">${t}</div>
                    `;
                    bubble.querySelector('.body').textContent = body;
                    box.appendChild(bubble);
                    box.scrollTop = box.scrollHeight;
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

