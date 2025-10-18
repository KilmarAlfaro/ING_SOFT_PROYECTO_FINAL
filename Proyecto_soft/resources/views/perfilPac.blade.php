<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Paciente</title>
    <link rel="stylesheet" href="{{ asset('css/perfilpac.css') }}">
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
            @if(!empty($paciente->foto_perfil) && file_exists(public_path('storage/profile_pics/' . $paciente->foto_perfil)))
                <img id="pacientePreview" src="{{ asset('storage/profile_pics/' . $paciente->foto_perfil) }}" alt="Foto de perfil" class="profile-pic">
            @else
                <img id="pacientePreview" src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Foto de perfil" class="profile-pic">
            @endif
            
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

        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input 
                type="text" 
                id="direccion" 
                name="direccion" 
                value="{{ old('direccion', $paciente->direccion ?? '') }}" 
                placeholder="Col. Centro, San Miguel"
            >
            @error('direccion') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

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

        <div class="acciones" style="gap:8px;">
            <button type="button" id="discardPaciente" class="btn btn-secundario">Descartar cambios</button>
            <button type="submit" class="btn btn-primario">Actualizar Perfil</button>
            <a href="{{ route('mainPac') }}" class="btn btn-secundario">Regresar</a>
        </div>
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