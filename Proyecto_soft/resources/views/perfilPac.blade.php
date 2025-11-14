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
            @php $pacienteFoto = route('avatar.paciente', $paciente->id); @endphp
            <img id="pacientePreview" src="{{ $pacienteFoto }}" alt="Foto de perfil" class="profile-pic">
            
            <label for="profile_image" class="file-upload-label">
                Cambiar foto de perfil
            </label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*">
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
                <button type="submit" class="btn btn-primario">Actualizar Perfil</button>
            </div>
        </div>

        <a href="{{ route('mainPac') }}" class="btn-regresar-full">Regresar</a>
    </form>
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