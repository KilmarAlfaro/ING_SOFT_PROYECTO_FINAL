<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Paciente</title>
    <link rel="stylesheet" href="{{ asset('css/perfilpac.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>

<div class="profile-container">
    <h2>Perfil del Paciente</h2>

    @if(session('success'))
        <div class="message success">
            {{ session('success') }}
        </div>
    @endif

    <form id="pacienteForm" action="{{ route('perfil.paciente.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf 

        <div class="profile-pic-container">
            @php 
                $ver = optional($paciente->updated_at)->timestamp ?? time();
                $pacienteFoto = route('avatar.paciente', $paciente->id) . '?v=' . $ver; 
            @endphp
            <img id="pacientePreview" src="{{ $pacienteFoto }}" alt="Foto de perfil" class="profile-pic">
            
            <label for="profile_image" class="file-upload-label">
                Cambiar foto de perfil
            </label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*">
            @error('profile_image') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="{{ old('nombre', $paciente->nombre ?? '') }}" 
                placeholder="Ingresa tu nombre"
            >
            @error('nombre') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input 
                type="text" 
                id="apellido" 
                name="apellido" 
                value="{{ old('apellido', $paciente->apellido ?? '') }}" 
                placeholder="Ingresa tu apellido"
            >
            @error('apellido') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input 
                type="text" 
                id="telefono" 
                name="telefono" 
                value="{{ old('telefono', $paciente->telefono ?? '') }}" 
                placeholder="1234-5678"
            >
            @error('telefono') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

        {{-- Dirección eliminada por petición: ya no es parte del perfil editable en registro --}}

        <div class="form-group">
            <label for="correo">Correo electrónico (login):</label>
            <input 
                type="email" 
                id="correo" 
                name="correo" 
                value="{{ old('correo', $paciente->correo ?? '') }}" 
                placeholder="ejemplo@correo.com"
            >
            @error('correo') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

        <!-- dummy field to discourage browser autofill -->
        <input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="position:absolute; left:-9999px; top:auto; width:1px; height:1px;" autocomplete="off">

        <div class="form-group">
            <label for="password">Nueva contraseña (opcional):</label>
            <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar" autocomplete="new-password" value="">
            @error('password') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar contraseña:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la nueva contraseña" autocomplete="new-password" value="">
        </div>

        <div class="actions-row">
            <div class="left">
                <button type="button" id="discardPaciente" class="btn btn-descartar">Descartar cambios</button>
            </div>
            <div class="right">
                <a href="{{ route('mainPac') }}" class="btn btn-regresar btn-regresar-inline">Regresar</a>
            </div>
        </div>

        <button type="submit" class="btn btn-primario btn-actualizar-full">Actualizar Perfil</button>
    </form>

    <div class="danger-zone">
        <h3>Eliminar cuenta</h3>
        <p>Esta acción borrará definitivamente tus datos personales, consultas y mensajes asociados. No podrás recuperarlos.</p>
        <form id="deletePacienteForm" action="{{ route('perfil.paciente.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="confirm_delete" value="yes">
            <button type="button" id="deletePacienteBtn" class="btn btn-peligro">Eliminar todos mis datos</button>
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
    // Paciente: preview and discard
    const pacProfileInput = document.getElementById('profile_image');
    const pacPreview = document.getElementById('pacientePreview');
    const pacOriginalSrc = pacPreview ? pacPreview.src : '';
    if (pacProfileInput) {
        pacProfileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = () => { if (pacPreview) pacPreview.src = reader.result; };
            reader.readAsDataURL(file);
        });
    }

    // Discard: reset inputs to initial values

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

    const deletePacienteBtn = document.getElementById('deletePacienteBtn');
    const deletePacienteForm = document.getElementById('deletePacienteForm');
    if (deletePacienteBtn && deletePacienteForm) {
        deletePacienteBtn.addEventListener('click', async (event) => {
            event.preventDefault();
            const ok = await showConfirmation('Esta acción eliminará permanentemente todos tus datos y consultas. ¿Deseas continuar?');
            if (ok) {
                deletePacienteForm.submit();
            }
        });
    }
    const pacForm = document.getElementById('pacienteForm');
    const discardBtn = document.getElementById('discardPaciente');
    const pacInputs = pacForm ? Array.from(pacForm.querySelectorAll('input, textarea, select')) : [];
    const pacInitial = {};
    pacInputs.forEach(i => { if (i.type !== 'file') pacInitial[i.name] = i.value; });
    if (discardBtn) {
        discardBtn.addEventListener('click', () => {
            pacInputs.forEach(i => {
                if (i.type === 'file') { i.value = ''; }
                else if (pacInitial.hasOwnProperty(i.name)) { i.value = pacInitial[i.name]; }
            });
            if (pacPreview) pacPreview.src = pacOriginalSrc;
        });
    }
</script>

</body>
</html>