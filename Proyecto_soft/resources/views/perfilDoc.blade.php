<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Doctor</title>
    <!-- Use patient profile styling for a cleaner, professional look -->
    <link rel="stylesheet" href="{{ asset('css/perfilpac.css') }}">
</head>
<body>
    <div class="profile-container">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <h2 style="margin:0">Mi perfil</h2>
            <a href="{{ route('mainDoc') }}" class="btn btn-secundario">Volver</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="doctorForm" action="{{ route('perfil.doctor.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <div class="grid">
                <div class="profile-pic-container" style="margin-bottom:18px; text-align:left;">
                    @if(!empty($doctor->foto_perfil) && file_exists(public_path('storage/profile_pics/' . $doctor->foto_perfil)))
                        <img id="doctorPreview" src="{{ asset('storage/profile_pics/' . $doctor->foto_perfil) }}" alt="Foto de perfil" class="profile-pic">
                    @else
                        <img id="doctorPreview" src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil" class="profile-pic">
                    @endif
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
                    <label for="direccion_clinica">Dirección de la clínica</label>
                    <input type="text" id="direccion_clinica" name="direccion_clinica" value="{{ old('direccion_clinica', $doctor->direccion_clinica ?? '') }}" required>
                </div>

                <div class="full-width"><hr></div>

                <div class="full-width"><h2>Datos de cuenta</h2></div>

                <!-- Campo 'usuario' eliminado: login por correo -->

                <div class="form-row">
                    <label for="password">Nueva contraseña (opcional)</label>
                    <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                </div>

                <div class="full-width footer-actions">
                    <div style="margin-right:auto;">
                        <button type="button" id="discardBtn" class="btn btn-secundario">Descartar cambios</button>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primario">Actualizar perfil</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
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
            });
        }
    </script>
</body>
</html>
