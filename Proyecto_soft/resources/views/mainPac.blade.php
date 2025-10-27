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
                {{-- Search card: inputs and filters. This form is intercepted by JS for live-search; it remains as a progressive enhancement and also works if JS is disabled. --}}
                <form id="searchForm" onsubmit="return false;">
                    {{-- Search by name (live) --}}
                    <div class="mb-2 b">
                        <input type="text" id="searchInput" name="q" class="busNom form-control form-control-sm" placeholder="Buscar por nombre" value="{{ $q }}" autocomplete="off">
                    </div>

                    {{-- Filter by specialty --}}
                    <div class="mb-2">
                        <select id="specialtySelect" name="especialidad" class="busNom form-select form-select-sm">
                            <option value="">Todas las especialidades</option>
                            @foreach(['General','Cardiologo','Cirujano plastico','Pediatra','Dermatologo','Ginecologo','Neurologo','Ortopedista','Oftalmologo','Psiquiatra','Otro'] as $esp)
                                <option value="{{ $esp }}" {{ (isset($especialidad) && $especialidad == $esp) ? 'selected' : '' }}>{{ $esp }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Reset filters button: full-width and visually aligned with the inputs above. --}}
                    <div class="mb-2">
                        <button id="resetFilters" class="btn btn-reset-filters">Restablecer filtros</button>
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

        // ---------- 2) Live search implementation ----------
        // Build a DOM node for a doctor (keeps same structure as server-rendered items)
        function buildDoctorItem(d){
            const li = document.createElement('li');
            li.className = 'chat-item';
            li.dataset.id = d.id;
            li.dataset.nombre = (d.nombre || '').split(' ')[0] || d.nombre || '';
            li.dataset.apellido = (d.apellido || '').split(' ')[0] || d.apellido || '';
            li.dataset.especialidad = d.especialidad || '';
            li.dataset.descripcion = d.descripcion || '';
            li.dataset.foto = d.foto || '';

            const thumb = document.createElement('div'); thumb.className = 'thumb';
            const img = document.createElement('img'); img.src = d.foto; img.alt = 'avatar'; thumb.appendChild(img);

            const meta = document.createElement('div'); meta.className = 'meta';
            const name = document.createElement('div'); name.className = 'name'; name.textContent = 'Dr(a). ' + li.dataset.nombre + ' ' + li.dataset.apellido;
            const sub = document.createElement('div'); sub.className = 'sub'; sub.textContent = d.especialidad || '';
            meta.appendChild(name); meta.appendChild(sub);

            const actions = document.createElement('div'); actions.className = 'actions';
            const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn btn-sm btn-outline-primary'; btn.textContent = 'Consultar';
            btn.addEventListener('click', function(e){ e.stopPropagation(); openConsultaModal(d.id); });
            actions.appendChild(btn);

            li.appendChild(thumb); li.appendChild(meta); li.appendChild(actions);
            // clicking the item itself opens the modal, delegation also handles this for server-rendered items
            li.addEventListener('click', function(e){ if (e.target && e.target.tagName.toLowerCase()==='button') return; openConsultaModal(d.id); });
            return li;
        }

        // Fetch JSON results and populate the left list
        async function performLiveSearch(q, especialidad){
            const params = new URLSearchParams();
            if (q) params.set('q', q);
            if (especialidad) params.set('especialidad', especialidad);
            const url = '{{ route('buscar.doctor') }}' + (params.toString() ? ('?' + params.toString()) : '');
            try {
                const res = await fetch(url, { headers:{ 'Accept':'application/json' } });
                if (!res.ok) return;
                const json = await res.json();
                const list = document.querySelector('.chat-list');
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

        // ---------- 3) Consulta modal open/close ----------
        function openConsultaModal(id) {
            // try to find a corresponding list item (may be server-rendered or built by JS)
            var li = document.querySelector('.chat-item[data-id="' + id + '"]');
            var foto = '{{ asset('imagenes/paciente.png') }}';
            var nombre = '';
            var apellido = '';
            var esp = '';
            var desc = '';
            if (li) {
                foto = li.dataset.foto || foto;
                nombre = li.dataset.nombre || '';
                apellido = li.dataset.apellido || '';
                esp = li.dataset.especialidad || '';
                desc = li.dataset.descripcion || '';
            }

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

            // Delegated click handler for server-rendered list items (and future ones)
            const listContainer = document.querySelector('.chat-list');
            if (listContainer){
                listContainer.addEventListener('click', function(e){
                    // find closest .chat-item
                    const item = e.target.closest('.chat-item');
                    if (!item) return;
                    // If click originated on a button inside actions, let its handler run (it stops propagation).
                    if (e.target && e.target.tagName && e.target.tagName.toLowerCase() === 'button') return;
                    const id = item.dataset.id;
                    if (id) openConsultaModal(id);
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
        });
    </script>

</body>
</html>