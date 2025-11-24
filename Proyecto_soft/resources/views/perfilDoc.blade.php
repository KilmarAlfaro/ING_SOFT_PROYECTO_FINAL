<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Doctor</title>
    <!-- Use patient profile styling for a cleaner, professional look -->
    <link rel="stylesheet" href="{{ asset('css/perfilpac.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
    <div class="profile-container">
        <h2 style="margin-bottom:12px">Mi perfil</h2>

        @php
            $estadoActual = old('estado', $doctor->estado ?? 'activo');
        @endphp

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        {{-- Mostrar errores inline junto a cada campo (se manejan más abajo) --}}

        <form id="doctorForm" action="{{ route('perfil.doctor.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <div class="grid">
                <div class="profile-pic-container" style="margin-bottom:18px; text-align:left;">
                    @php 
                        $ver = optional($doctor->updated_at)->timestamp ?? time();
                        $doctorFoto = route('avatar.doctor', $doctor->id) . '?v=' . $ver; 
                    @endphp
                    <img id="doctorPreview" src="{{ $doctorFoto }}" alt="Foto de perfil" class="profile-pic">
                    <div style="margin-top:8px;">
                        <label for="profile_image" class="file-upload-label">Cambiar foto de perfil</label>
                        <input type="file" name="profile_image" id="profile_image" accept="image/*">
                        @error('profile_image') <div class="message" style="color:red">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-row">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $doctor->nombre ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $doctor->apellido ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" value="{{ old('correo', $doctor->correo ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $doctor->telefono ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="especialidad">Especialidad</label>
                    <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad', $doctor->especialidad ?? '') }}" required>
                </div>

                <div class="form-row">
                    <label for="numero_colegiado">Número de colegiado</label>
                    <input type="text" id="numero_colegiado" name="numero_colegiado" value="{{ old('numero_colegiado', $doctor->numero_colegiado ?? '') }}" required>
                </div>

                <div class="form-row full-width">
                    <label for="descripcion">Descripción profesional</label>
                    <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000" placeholder="Comparte tu experiencia, especialidades y logros destacados">{{ old('descripcion', $doctor->descripcion ?? '') }}</textarea>
                    @error('descripcion') <div class="message" style="color:red">{{ $message }}</div> @enderror
                </div>

                <div class="form-row full-width">
                    <label for="direccion_clinica">Dirección de la clínica</label>
                    <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica', $doctor->direccion_clinica ?? '') }}" required>
                </div>

                <div class="form-row full-width status-card">
                    <h3>Visibilidad para pacientes</h3>
                    <p class="status-hint">Activa tu perfil para que aparezca en los resultados de búsqueda. Si lo marcas como inactivo, los pacientes no podrán verte ni realizar nuevas consultas.</p>
                    <div class="status-legend">
                        <span><strong>Activo:</strong> Visible para los pacientes.</span>
                        <span><strong>Inactivo:</strong> Oculto para los pacientes.</span>
                    </div>
                    <div class="status-toggle" role="group" aria-label="Estado del perfil">
                        <input type="hidden" name="estado" id="estadoInput" value="{{ $estadoActual }}">
                        <button type="button" class="toggle-option {{ $estadoActual === 'activo' ? 'is-selected' : '' }}" data-value="activo">
                            <span class="dot dot-active"></span>
                            Activo
                        </button>
                        <button type="button" class="toggle-option {{ $estadoActual === 'inactivo' ? 'is-selected' : '' }}" data-value="inactivo">
                            <span class="dot dot-inactive"></span>
                            Inactivo
                        </button>
                    </div>
                    @error('estado') <div class="message" style="color:red">{{ $message }}</div> @enderror
                </div>

                <div class="full-width"><hr></div>

                <div class="full-width"><h2>Datos de cuenta</h2></div>

                <!-- Campo 'usuario' eliminado: login por correo -->

                {{-- dummy field to discourage browser autofill for passwords --}}
                <input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="position:absolute; left:-9999px; top:auto; width:1px; height:1px;" autocomplete="off">

                <div class="form-row">
                    <label for="password">Nueva contraseña (opcional)</label>
                    <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar" autocomplete="new-password" value="">
                    @error('password') <div class="message" style="color:red">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <label for="password_confirmation">Confirmar nueva contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la nueva contraseña" autocomplete="new-password" value="">
                    @error('password_confirmation') <div class="message" style="color:red">{{ $message }}</div> @enderror
                </div>

                <div class="actions-row full-width">
                    <div class="left">
                        <button type="button" id="discardBtn" class="btn btn-descartar">Descartar cambios</button>
                    </div>
                    <div class="right">
                        <a href="{{ route('mainDoc') }}" class="btn btn-regresar btn-regresar-inline">Regresar</a>
                    </div>
                </div>

                <button type="submit" id="submitProfile" class="btn btn-primario btn-actualizar-full">Actualizar perfil</button>
            </div>
        </form>

        <div class="danger-zone">
            <h3>Eliminar cuenta</h3>
            <p>Esta acción borrará definitivamente tu perfil, credenciales y consultas asociadas. No podrás recuperarlos.</p>
            <form id="deleteAccountForm" action="{{ route('perfil.doctor.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="confirm_delete" value="yes">
                <button type="button" id="deleteAccountBtn" class="btn btn-peligro">Eliminar toda mi cuenta</button>
            </form>
        </div>
    </div>

    <div id="confirmationModal" class="confirm-modal" aria-hidden="true">
        <div class="confirm-modal__content">
            <h3>Confirma tu acción</h3>
            <p id="confirmMessage" class="confirm-modal__message">¿Deseas continuar?</p>
            <div class="confirm-modal__actions">
                <button type="button" class="btn btn-secundario" id="confirmCancel">Cancelar</button>
                <button type="button" class="btn btn-primario" id="confirmAccept">Continuar</button>
            </div>
        </div>
    </div>

    <script>
        const confirmationModal = document.getElementById('confirmationModal');
        const confirmMessageEl = document.getElementById('confirmMessage');
        const confirmAcceptBtn = document.getElementById('confirmAccept');
        const confirmCancelBtn = document.getElementById('confirmCancel');

        const showConfirmation = (message) => {
            return new Promise((resolve) => {
                if (!confirmationModal || !confirmMessageEl || !confirmAcceptBtn || !confirmCancelBtn) {
                    resolve(window.confirm(message));
                    return;
                }

                confirmMessageEl.textContent = message;
                confirmationModal.classList.add('is-visible');
                confirmationModal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');

                const cleanup = (result) => {
                    confirmationModal.classList.remove('is-visible');
                    confirmationModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                    confirmAcceptBtn.removeEventListener('click', onAccept);
                    confirmCancelBtn.removeEventListener('click', onCancel);
                    confirmationModal.removeEventListener('click', onBackdrop);
                    document.removeEventListener('keydown', onKeyDown);
                    resolve(result);
                };

                const onAccept = () => cleanup(true);
                const onCancel = () => cleanup(false);
                const onBackdrop = (event) => { if (event.target === confirmationModal) cleanup(false); };
                const onKeyDown = (event) => { if (event.key === 'Escape') cleanup(false); };

                confirmAcceptBtn.addEventListener('click', onAccept);
                confirmCancelBtn.addEventListener('click', onCancel);
                confirmationModal.addEventListener('click', onBackdrop);
                document.addEventListener('keydown', onKeyDown);
            });
        };

        // Image preview
        const profileInput = document.getElementById('profile_image');
        const previewImg = document.getElementById('doctorPreview');
        const originalPreviewSrc = previewImg ? previewImg.src : '';

        if (profileInput) {
            profileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = () => {
                    if (previewImg) previewImg.src = reader.result;
                };
                reader.readAsDataURL(file);
            });
        }

        // Discard changes: reset form and preview
        const discardBtn = document.getElementById('discardBtn');
        const form = document.getElementById('doctorForm');
        const inputs = form ? Array.from(form.querySelectorAll('input, textarea, select')) : [];
        const estadoInput = document.getElementById('estadoInput');
        const toggleButtons = document.querySelectorAll('.status-toggle .toggle-option');
        // capture initial values
        const initialValues = {};
        inputs.forEach(i => { if (i.type !== 'file') initialValues[i.name] = i.value; });

        if (discardBtn) {
            discardBtn.addEventListener('click', () => {
                inputs.forEach(i => {
                    if (i.type === 'file') { i.value = ''; }
                    else if (initialValues.hasOwnProperty(i.name)) { i.value = initialValues[i.name]; }
                });
                if (previewImg) previewImg.src = originalPreviewSrc;
                if (estadoInput && toggleButtons.length) {
                    toggleButtons.forEach(btn => {
                        btn.classList.toggle('is-selected', btn.dataset.value === estadoInput.value);
                    });
                }
            });
        }

        // Confirm availability change when estado is toggled

        if (toggleButtons.length && estadoInput) {
            toggleButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const value = btn.dataset.value;
                    estadoInput.value = value;
                    toggleButtons.forEach(b => b.classList.remove('is-selected'));
                    btn.classList.add('is-selected');
                });
            });
        }

        if (estadoInput && form) {
            const initialEstado = estadoInput.value;
            let estadoConfirmed = false;
            form.addEventListener('submit', async (event) => {
                if (!estadoConfirmed && estadoInput.value !== initialEstado) {
                    event.preventDefault();
                    const message = estadoInput.value === 'activo'
                        ? 'Tu perfil volverá a mostrarse a los pacientes. ¿Deseas continuar?'
                        : 'Tu perfil quedará oculto y los pacientes no podrán encontrarte. ¿Deseas continuar?';
                    const ok = await showConfirmation(message);
                    if (ok) {
                        estadoConfirmed = true;
                        form.submit();
                    }
                }
            });
        }

        // Delete account confirmation
        const deleteBtn = document.getElementById('deleteAccountBtn');
        const deleteForm = document.getElementById('deleteAccountForm');
        if (deleteBtn && deleteForm) {
            deleteBtn.addEventListener('click', async (event) => {
                event.preventDefault();
                const ok = await showConfirmation('Esta acción eliminará todos tus datos y no se puede deshacer. ¿Deseas continuar?');
                if (ok) {
                    deleteForm.submit();
                }
            });
        }
    </script>
</body>
</html>
