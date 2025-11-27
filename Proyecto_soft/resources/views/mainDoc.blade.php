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
        .dashboard-navbar .navbar-logo:hover { transform: none; }
        .dashboard-navbar .brand-text { display: flex; flex-direction: column; line-height: 1.1; }
        .dashboard-navbar .brand-title { font-size: 1.4rem; font-weight: 700; letter-spacing: 0.03em; }
        .dashboard-navbar .brand-subtitle { font-size: 0.85rem; color: rgba(248, 250, 252, 0.9); }
        .dashboard-navbar .navbar-actions { display: flex; align-items: center; gap: 16px; }
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
        .dashboard-navbar .help-chip:hover { background: rgba(15, 23, 42, 0.16); }
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
            .dashboard-navbar { flex-direction: column; gap: 14px; }
            .dashboard-navbar .navbar-actions { width: 100%; justify-content: flex-end; flex-wrap: wrap; }
        }
        .tag-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: none;
            background: #e2e8f0;
            color: #fff;
            margin-left: 6px;
        }
        .tag-badge--muted { background: #e2e8f0; color: #475569; }
        .tag-anchor { display: inline-flex; align-items: center; min-height: 18px; }
        .filter-mode-toggle {
            display: inline-flex;
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 8px;
        }
        .filter-mode-toggle button {
            border: none;
            background: transparent;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #0f172a;
            cursor: pointer;
        }
        .filter-mode-toggle button.is-active { background: rgba(15, 23, 42, 0.08); }
        .tag-manager {
            background: #f8fafc;
            border: 1px dashed #cbd5f5;
            border-radius: 14px;
            padding: 12px;
            margin: 12px 0;
        }
        .tag-manager.is-disabled { opacity: 0.55; }
        .tag-manager__summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .tag-manager__summary-label { font-size: 0.85rem; color: #1f2937; display: flex; align-items: center; gap: 8px; font-weight: 600; }
        .tag-manager__summary-label .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #0f6fc6;
            display: inline-block;
        }
        .tag-manager__toggle {
            border: none;
            background: transparent;
            color: #0f6fc6;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 0;
        }
        .tag-manager__toggle .chevron { transition: transform 0.2s ease; }
        .tag-manager.is-open .tag-manager__toggle .chevron { transform: rotate(180deg); }
        .tag-manager__status {
            font-size: 0.78rem;
            color: #475569;
            margin: 4px 0 0;
            min-height: 1em;
        }
        .tag-popover {
            display: none;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5f5;
        }
        .tag-popover.is-open { display: block; }
        .tag-panel__help { font-size: 0.78rem; color: #475569; margin: 6px 0; }
        .tag-panel__presets { display: flex; flex-wrap: wrap; gap: 8px; margin: 4px 0 10px; }
        .tag-chip {
            border: none;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15);
        }
        .tag-custom-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        #tagCustomInput { flex: 1; min-width: 200px; padding: 8px 10px; border-radius: 8px; border: 1px solid #cbd5f5; }
        .tag-color-picker { display: flex; gap: 8px; flex-wrap: wrap; }
        .tag-color-dot {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
            cursor: pointer;
        }
        .tag-color-dot.is-selected { outline: 2px solid #0f172a; }
        .tag-panel__actions { display: flex; gap: 8px; margin-top: 10px; }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        .tag-inline { margin-top: 4px; }
        .section-heading { margin: 18px 0 8px; font-size: 1.2rem; color: #0f172a; }
        .filter-wrap { display: flex; align-items: center; gap: 10px; }
        .filter-wrap--block { margin-bottom: 14px; }
        .filter-wrap .list-filter { min-width: 240px; flex: 1; }
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
            <h2 class="section-heading">Consultas</h2>
            <div class="filter-mode-toggle" role="group" aria-label="Modo de filtrado" id="filterModeToggle">
                <button type="button" class="is-active" data-mode="nombre">Por nombre</button>
                <button type="button" data-mode="tag">Por etiqueta</button>
            </div>
            <div class="filter-wrap filter-wrap--block">
                <input id="filtroActivas" type="text" class="list-filter" placeholder="Filtrar por paciente">
                <select id="selectorActivas" class="list-filter" style="display:none;">
                    <option value="">Todas las etiquetas</option>
                </select>
                <button type="button" class="clear-filter-btn" aria-label="Limpiar filtro" onclick="limpiarFiltro('filtroActivas','selectorActivas')">
                    <img src="https://cdn2.iconfinder.com/data/icons/css-vol-3/24/trash-256.png" alt="Borrar" />
                </button>
            </div>
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
                        $tagLabel = $c->tag_label;
                        $tagColor = $c->tag_color ?: '#2563eb';
                    @endphp
                    <li class="chat-item"
                        data-id="{{ $c->id }}"
                        data-paciente="{{ $fn }} {{ $fl }}"
                        data-foto="{{ $fotoUrl }}"
                        data-mensaje="{{ e($c->mensaje) }}"
                        data-respuesta="{{ e($c->respuesta) }}"
                        data-status="{{ $c->status ?? 'nuevo' }}"
                        data-tag-label="{{ $tagLabel }}"
                        data-tag-color="{{ $c->tag_color ?? '' }}">
                        <div class="thumb"><img src="{{ $fotoUrl }}" alt="avatar"></div>
                        <div class="meta">
                            <div class="name">
                                {{ $fn }} {{ $fl }}
                                <span class="tag-anchor">
                                    @if($tagLabel)
                                        <span class="tag-badge" style="background: {{ $tagColor }}">{{ $tagLabel }}</span>
                                    @endif
                                </span>
                            </div>
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
            <h2 class="section-heading">Consultas finalizadas</h2>
            <div class="filter-mode-toggle" role="group" aria-label="Modo de filtrado" id="filterModeToggleFinal">
                <button type="button" class="is-active" data-mode="nombre">Por nombre</button>
                <button type="button" data-mode="tag">Por etiqueta</button>
            </div>
            <div class="filter-wrap filter-wrap--block">
                <input id="filtroFinalizadas" type="text" class="list-filter" placeholder="Filtrar por paciente">
                <select id="selectorFinalizadas" class="list-filter" style="display:none;">
                    <option value="">Todas las etiquetas</option>
                </select>
                <button type="button" class="clear-filter-btn" aria-label="Limpiar filtro" onclick="limpiarFiltro('filtroFinalizadas','selectorFinalizadas')">
                    <img src="https://cdn2.iconfinder.com/data/icons/css-vol-3/24/trash-256.png" alt="Borrar" />
                </button>
            </div>
            <ul class="consultas-list chat-list" id="listaFinalizadas">
                @forelse($consultasFinalizadas as $c)
                    @php
                        $pac = $c->paciente;
                        $fn = $pac ? (explode(' ', trim($pac->nombre))[0] ?? $pac->nombre) : 'Paciente';
                        $fl = $pac ? (explode(' ', trim($pac->apellido))[0] ?? ($pac->apellido ?? '')) : '';
                        $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
                        $ver = optional($pac?->updated_at)->timestamp ?? time();
                        $fotoUrl = ($pac && $pac->id) ? route('avatar.paciente', $pac->id) . '?v=' . $ver : $defaultAvatar;
                        $tagLabel = $c->tag_label;
                        $tagColor = $c->tag_color ?: '#2563eb';
                    @endphp
                    <li class="chat-item"
                        data-id="{{ $c->id }}"
                        data-paciente="{{ $fn }} {{ $fl }}"
                        data-foto="{{ $fotoUrl }}"
                        data-status="finalizado"
                        data-tag-label="{{ $tagLabel }}"
                        data-tag-color="{{ $c->tag_color ?? '' }}">
                        <div class="thumb"><img src="{{ $fotoUrl }}" alt="avatar"></div>
                        <div class="meta">
                            <div class="name">
                                {{ $fn }} {{ $fl }}
                                <span class="tag-anchor">
                                    @if($tagLabel)
                                        <span class="tag-badge" style="background: {{ $tagColor }}">{{ $tagLabel }}</span>
                                    @endif
                                </span>
                            </div>
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
        const TAG_PRESETS = [
            { label: 'Urgente', color: '#dc2626' },
            { label: 'Seguimiento', color: '#2563eb' },
            { label: 'Revisión pendiente', color: '#f97316' },
            { label: 'Laboratorios', color: '#7c3aed' },
            { label: 'Coordinar cita', color: '#16a34a' },
        ];
        const TAG_COLORS = ['#2563eb', '#dc2626', '#16a34a', '#7c3aed', '#0891b2', '#f97316'];
        const filterConfigs = [];
        const CONSULTAS_BASE = `{{ url('/consultas') }}`;
        let currentConsultaId = null;
        let currentTagLabel = '';
        let currentTagColor = '#2563eb';
        let tagCustomColor = TAG_COLORS[0];
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

        function setCurrentConsultaContext(id, label, color){
            currentConsultaId = id;
            currentTagLabel = label || '';
            currentTagColor = color || '#2563eb';
        }

        function setTagPanelMessage(message, persistent = false){
            const statusEl = document.getElementById('tagPanelStatus');
            if (!statusEl) return;
            statusEl.dataset.persistent = persistent ? 'true' : 'false';
            statusEl.textContent = message || '';
            if (!persistent && message){
                setTimeout(() => {
                    if (statusEl.dataset.persistent === 'true') return;
                    statusEl.textContent = '';
                }, 4000);
            }
        }

        function updateDetailTagBadge(label, color){
            const holder = document.getElementById('docChatTagHolder');
            const badge = document.getElementById('tagCurrentBadge');
            const resolvedColor = color || '#2563eb';
            if (holder){
                holder.innerHTML = label ? `<span class="tag-badge" style="background:${resolvedColor}">${escapeHtml(label)}</span>` : '';
            }
            if (badge){
                if (label){
                    badge.classList.remove('tag-badge--muted');
                    badge.style.background = resolvedColor;
                    badge.textContent = label;
                } else {
                    badge.classList.add('tag-badge--muted');
                    badge.style.background = '';
                    badge.textContent = 'Sin etiqueta';
                }
            }
        }

        function closeTagPopover(){
            const panel = document.getElementById('tagPanel');
            const popover = document.getElementById('tagPopover');
            const toggleBtn = document.getElementById('tagToggleBtn');
            if (popover) popover.classList.remove('is-open');
            if (panel) panel.classList.remove('is-open');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        }

        function renderTagPresetButtons(disabled){
            const presetsContainer = document.getElementById('tagPresetButtons');
            if (!presetsContainer) return;
            presetsContainer.innerHTML = TAG_PRESETS.map(p => `<button type="button" class="tag-chip" data-label="${p.label}" data-color="${p.color}" style="background:${p.color}">${p.label}</button>`).join('');
            const buttons = presetsContainer.querySelectorAll('button');
            buttons.forEach(btn => {
                btn.disabled = disabled;
                if (!disabled){
                    btn.addEventListener('click', () => applyTag(btn.dataset.label, btn.dataset.color));
                }
            });
        }

        function renderTagColorPicker(disabled){
            const picker = document.getElementById('tagColorPicker');
            if (!picker) return;
            picker.innerHTML = TAG_COLORS.map(color => `<button type="button" class="tag-color-dot${color === tagCustomColor ? ' is-selected' : ''}" data-color="${color}" style="background:${color}"></button>`).join('');
            picker.querySelectorAll('button').forEach(btn => {
                btn.disabled = disabled;
                if (!disabled){
                    btn.addEventListener('click', () => {
                        tagCustomColor = btn.dataset.color;
                        picker.querySelectorAll('button').forEach(b => b.classList.toggle('is-selected', b === btn));
                    });
                }
            });
        }

        function setupTagPanel({ id, status, label, color }){
            const normalizedLabel = label || '';
            const normalizedColor = color || '#2563eb';
            setCurrentConsultaContext(id, normalizedLabel, normalizedColor);
            const panel = document.getElementById('tagPanel');
            const popover = document.getElementById('tagPopover');
            const toggleBtn = document.getElementById('tagToggleBtn');
            if (!panel) return;
            closeTagPopover();
            const disabled = status === 'finalizado';
            panel.classList.toggle('is-disabled', disabled);
            panel.setAttribute('aria-disabled', disabled ? 'true' : 'false');
            if (toggleBtn){
                toggleBtn.disabled = disabled;
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.onclick = () => {
                    if (toggleBtn.disabled || !popover) return;
                    const open = !popover.classList.contains('is-open');
                    popover.classList.toggle('is-open', open);
                    panel.classList.toggle('is-open', open);
                    toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                };
            }
            if (normalizedLabel && TAG_COLORS.includes(normalizedColor)){
                tagCustomColor = normalizedColor;
            } else if (!normalizedLabel) {
                tagCustomColor = TAG_COLORS[0];
            }
            if (disabled){
                setTagPanelMessage('Esta consulta está finalizada; la etiqueta es de solo lectura.', true);
            } else {
                setTagPanelMessage('', false);
            }
            const customInput = document.getElementById('tagCustomInput');
            if (customInput){
                customInput.value = '';
                customInput.disabled = disabled;
            }
            renderTagPresetButtons(disabled);
            renderTagColorPicker(disabled);
            const applyBtn = document.getElementById('applyCustomTag');
            if (applyBtn){
                applyBtn.disabled = disabled;
                applyBtn.onclick = () => {
                    if (disabled) return;
                    const value = customInput ? (customInput.value || '').trim() : '';
                    if (!value){
                        setTagPanelMessage('Ingresa una etiqueta personalizada antes de guardar.', false);
                        return;
                    }
                    if (value.length > 20){
                        setTagPanelMessage('La etiqueta no puede exceder 20 caracteres.', false);
                        return;
                    }
                    applyTag(value, tagCustomColor);
                };
            }
            const clearBtn = document.getElementById('clearTagBtn');
            if (clearBtn){
                clearBtn.disabled = disabled;
                clearBtn.onclick = () => {
                    if (disabled) return;
                    applyTag('', null);
                };
            }
            updateDetailTagBadge(normalizedLabel, normalizedColor);
        }

        function applyTag(label, color){
            if (!currentConsultaId) return;
            const trimmed = (label || '').trim();
            if (trimmed && trimmed.length > 20){
                setTagPanelMessage('La etiqueta no puede exceder 20 caracteres.', false);
                return;
            }
            persistTag(trimmed, trimmed ? (color || tagCustomColor || '#2563eb') : null);
        }

        async function persistTag(label, color){
            try {
                const response = await fetch(`${CONSULTAS_BASE}/${currentConsultaId}/tag`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ label: label || null, color: label ? (color || '#2563eb') : null })
                });
                if (!response.ok) {
                    throw new Error('No se pudo actualizar la etiqueta.');
                }
                const payload = await response.json();
                const payloadData = payload && payload.data ? payload.data : {};
                const updatedLabel = payloadData.tag_label || '';
                const updatedColor = payloadData.tag_color || '#2563eb';
                setCurrentConsultaContext(currentConsultaId, updatedLabel, updatedColor);
                tagCustomColor = updatedColor || TAG_COLORS[0];
                updateDetailTagBadge(updatedLabel, updatedColor);
                syncTagBadgeForItems(currentConsultaId, updatedLabel, updatedColor);
                setTagPanelMessage(updatedLabel ? 'Etiqueta aplicada correctamente.' : 'Etiqueta eliminada.', false);
                const customInput = document.getElementById('tagCustomInput');
                if (customInput) customInput.value = '';
                closeTagPopover();
                renderTagColorPicker(false);
            } catch (error) {
                console.error(error);
                setTagPanelMessage('Error al guardar la etiqueta. Inténtelo de nuevo.', false);
            }
        }

        function syncTagBadgeForItems(id, label, color){
            document.querySelectorAll(`.chat-item[data-id="${id}"]`).forEach(li => {
                li.dataset.tagLabel = label || '';
                li.dataset.tagColor = color || '';
                const anchor = li.querySelector('.tag-anchor');
                if (anchor){
                    anchor.innerHTML = label ? `<span class="tag-badge" style="background:${color || '#2563eb'}">${escapeHtml(label)}</span>` : '';
                }
            });
            refreshAllTagSelectOptions();
            if (filterMode === 'tag') reapplyFilters();
        }

        async function mostrarDetalle(id){
            const item = document.querySelector('.chat-item[data-id="'+id+'"]');
            if (!item) return;
            const nombre = item.dataset.paciente || 'Paciente';
            const foto = item.dataset.foto || 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png';
            const status = item.dataset.status || 'nuevo';
            const initialTagLabel = item.dataset.tagLabel || '';
            const initialTagColor = item.dataset.tagColor || '';
            setCurrentConsultaContext(id, initialTagLabel, initialTagColor || '#2563eb');

            const detalle = document.getElementById('consulta-detalle');
            detalle.innerHTML = `
                <div class="cabecera-consulta" id="docChatHeader" style="display:flex;align-items:center;gap:10px;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <img id="docChatFoto" src="${foto}" alt="avatar" class="avatar" width="44" height="44">
                        <div>
                            <div class="name" id="docChatTitle">Consulta de ${nombre}</div>
                            <div class="tag-inline" id="docChatTagHolder"></div>
                            <div class="subtle" id="docChatId">ID #${id}</div>
                            <div class="subtle" id="motivoLinea" style="margin-top:2px;"></div>
                        </div>
                    </div>
                    <span style="flex:1"></span>
                </div>
                <div class="tag-manager" id="tagPanel" aria-live="polite">
                    <div class="tag-manager__summary">
                        <div class="tag-manager__summary-label">
                            <span class="dot" aria-hidden="true"></span>
                            <span>Gestor de etiquetas:</span>
                            <span id="tagCurrentBadge" class="tag-badge tag-badge--muted">Sin etiqueta</span>
                        </div>
                        <button type="button" class="tag-manager__toggle" id="tagToggleBtn" aria-expanded="false">
                            Gestionar etiqueta
                            <span class="chevron">▾</span>
                        </button>
                    </div>
                    <p class="tag-panel__help">Úsala solo si necesitas ordenar o priorizar tus consultas.</p>
                    <p class="tag-manager__status" id="tagPanelStatus"></p>
                    <div class="tag-popover" id="tagPopover">
                        <div class="tag-panel__presets" id="tagPresetButtons"></div>
                        <div class="tag-panel__custom">
                            <label for="tagCustomInput">Etiqueta personalizada</label>
                            <div class="tag-custom-row">
                                <input id="tagCustomInput" type="text" maxlength="20" placeholder="Ej. Control mensual">
                                <div class="tag-color-picker" id="tagColorPicker"></div>
                            </div>
                            <div class="tag-panel__actions">
                                <button type="button" class="btn btn-primario btn-sm" id="applyCustomTag">Guardar</button>
                                <button type="button" class="btn btn-secundario btn-sm" id="clearTagBtn">Quitar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="docChatEmpty" class="text-muted" style="padding:8px 4px;display:none;">Sin mensajes todavía.</div>
                <div class="chat-box" id="chatBox" aria-live="polite"></div>
                <form id="docChatForm" class="respuesta-form" onsubmit="return enviarMensaje(event, ${id}, this)">
                    <textarea id="docChatInput" name="body" placeholder="Escribe tu respuesta..." required></textarea>
                </form>
                <div class="acciones-consulta" id="accionesConsulta"></div>
                <div id="docChatNote" class="subtle" style="display:none">Consulta finalizada (solo lectura)</div>
            `;
            setupTagPanel({ id, status, label: initialTagLabel, color: initialTagColor });

            // cargar mensajes
            try {
                const res = await fetch(`{{ url('/consultas') }}/${id}/mensajes`, { headers:{ 'Accept':'application/json' } });
                const data = await res.json();
                const consultaInfo = data && data.consulta ? data.consulta : null;
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
                    const motivoFromConsulta = consultaInfo && consultaInfo.motivo ? consultaInfo.motivo : null;
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
                if (consultaInfo){
                    setupTagPanel({
                        id,
                        status: consultaInfo.status || status,
                        label: consultaInfo.tag_label || '',
                        color: consultaInfo.tag_color || ''
                    });
                    syncTagBadgeForItems(id, consultaInfo.tag_label || '', consultaInfo.tag_color || '');
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

        function limpiarFiltro(textId, selectId){
            const textInput = textId ? document.getElementById(textId) : null;
            const select = selectId ? document.getElementById(selectId) : null;
            if (textInput){
                textInput.value = '';
                textInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (select){
                select.value = '';
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const selectVisible = select && select.style.display !== 'none';
            if (selectVisible) select.focus();
            else if (textInput) textInput.focus();
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

        function applyFilterForConfig(config){
            const listUl = config && config.listUl;
            if (!listUl) return;
            const mode = config.mode === 'tag' ? 'tag' : 'nombre';
            if (mode === 'tag'){
                const selected = (config.tagSelect ? config.tagSelect.value : '').toLowerCase();
                listUl.querySelectorAll('.chat-item').forEach(li => {
                    const label = (li.dataset.tagLabel || '').toLowerCase();
                    li.style.display = !selected || label === selected ? '' : 'none';
                });
            } else {
                const query = (config.textInput ? config.textInput.value : '').trim().toLowerCase();
                listUl.querySelectorAll('.chat-item').forEach(li => {
                    const haystack = (li.dataset.paciente || '').toLowerCase();
                    li.style.display = haystack.includes(query) ? '' : 'none';
                });
            }
        }

        function reapplyFilters(){
            filterConfigs.forEach(applyFilterForConfig);
        }

        function collectTagOptions(listUl){
            const tags = new Set();
            if (!listUl) return [];
            listUl.querySelectorAll('.chat-item').forEach(li => {
                const label = (li.dataset.tagLabel || '').trim();
                if (label) tags.add(label);
            });
            return Array.from(tags).sort((a,b) => a.localeCompare(b, 'es', { sensitivity: 'base' }));
        }

        function rebuildTagOptions(config){
            if (!config || !config.tagSelect) return;
            const select = config.tagSelect;
            const current = select.value;
            const options = collectTagOptions(config.listUl);
            let html = '<option value="">Todas las etiquetas</option>';
            options.forEach(label => { html += `<option value="${label}">${label}</option>`; });
            select.innerHTML = html;
            if (options.includes(current)){
                select.value = current;
            } else {
                select.value = '';
            }
        }

        function refreshAllTagSelectOptions(){
            filterConfigs.forEach(config => {
                rebuildTagOptions(config);
                if (config.mode === 'tag') applyFilterForConfig(config);
            });
        }

        function updateFilterControlVisibility(config){
            if (!config || !config.textInput || !config.tagSelect) return;
            const showTag = config.mode === 'tag';
            config.textInput.style.display = showTag ? 'none' : '';
            config.tagSelect.style.display = showTag ? '' : 'none';
        }

        function bindToggle(config, toggle){
            if (!config || !toggle) return;
            const buttons = toggle.querySelectorAll('button[data-mode]');
            const setActive = (mode) => {
                buttons.forEach(btn => btn.classList.toggle('is-active', btn.dataset.mode === mode));
            };
            toggle.addEventListener('click', (event) => {
                const btn = event.target.closest('button[data-mode]');
                if (!btn || btn.classList.contains('is-active')) return;
                const mode = btn.dataset.mode === 'tag' ? 'tag' : 'nombre';
                config.mode = mode;
                setActive(mode);
                updateFilterControlVisibility(config);
                applyFilterForConfig(config);
            });
            setActive(config.mode || 'nombre');
        }

        // Delegar click en toda la fila de consulta activa
        document.addEventListener('DOMContentLoaded', function(){
            const list = document.querySelector('.sidebar-left .chat-list');
            if (list){
                list.addEventListener('click', function(e){
                    const item = e.target.closest('.chat-item');
                    if (!item) return;
                    const id = item.getAttribute('data-id');
                    if (id) mostrarDetalle(id);
                });
            }
            const finList = document.querySelector('#sidebarRight .chat-list');
            if (finList){
                finList.addEventListener('click', function(e){
                    const item = e.target.closest('.chat-item');
                    if (!item) return;
                    const id = item.getAttribute('data-id');
                    if (id) mostrarDetalle(id);
                });
            }

            const registerFilter = (textId, selectId, listId, toggleId) => {
                const textInput = document.getElementById(textId);
                const tagSelect = document.getElementById(selectId);
                const listUl = document.getElementById(listId);
                const toggle = document.getElementById(toggleId);
                if (!listUl || !textInput || !tagSelect || !toggle) return;
                const config = { textInput, tagSelect, listUl, toggle, mode: 'nombre' };
                if (textInput){ textInput.addEventListener('input', () => applyFilterForConfig(config)); }
                if (tagSelect){ tagSelect.addEventListener('change', () => applyFilterForConfig(config)); }
                filterConfigs.push(config);
                rebuildTagOptions(config);
                bindToggle(config, toggle);
                updateFilterControlVisibility(config);
                applyFilterForConfig(config);
            };
            registerFilter('filtroActivas', 'selectorActivas', 'listaActivas', 'filterModeToggle');
            registerFilter('filtroFinalizadas', 'selectorFinalizadas', 'listaFinalizadas', 'filterModeToggleFinal');

            refreshAllTagSelectOptions();
        });

        function cerrarDetalle(){
            const detalle = document.getElementById('consulta-detalle');
            if (!detalle) return;
            detalle.innerHTML = '<p>Seleccione una consulta en la columna izquierda para ver los detalles.</p>';
        }
    </script>

</body>
</html>

