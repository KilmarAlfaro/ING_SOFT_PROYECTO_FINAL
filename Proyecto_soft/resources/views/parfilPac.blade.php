<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Paciente</title>
    
    {{-- Enlaza el archivo de estilos externo --}}
    <link rel="stylesheet" href="{{ asset('css/perlfilpac.css') }}"> 
</head>
<body>

<div class="profile-container">
    <h2>Perfil del Paciente</h2>

    {{-- Muestra el mensaje de éxito enviado por el controlador --}}
    @if(session('success'))
        <div class="message success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Formulario que usa la ruta nombrada y el token de seguridad --}}
    <form action="{{ route('perfil.paciente.update') }}" method="POST" enctype="multipart/form-data">
        @csrf 
        
        {{-- Sección de la foto de perfil --}}
        <div class="profile-pic-container">
            <img 
                {{-- Usa la ruta de la foto actual del paciente o un default --}}
                src="{{ asset('storage/profile_pics/' . ($paciente->foto_perfil ?? 'default.png')) }}"
                alt="Foto de perfil" 
                class="profile-pic"
            >
            
            <label for="profile_image" class="file-upload-label">
                Cambiar foto de perfil
            </label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*">
        </div>

        {{-- Campo Nombre --}}
        <div class="form-group">
            <label for="nombre">Nombre Completo:</label>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="{{ old('nombre', $paciente->nombre) }}" 
                placeholder="Ingresa tu nombre"
            >
            @error('nombre') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

        {{-- Campo Descripción (Estado) --}}
        <div class="form-group">
            <label for="descripcion">Descripción (Estado de WhatsApp):</label>
            <textarea 
                id="descripcion" 
                name="descripcion" 
                placeholder="Escribe algo sobre ti..."
            >{{ old('descripcion', $paciente->descripcion) }}</textarea> 
            @error('descripcion') <div class="message" style="color:red">{{ $message }}</div> @enderror
        </div>

        <button type="submit">Actualizar Perfil</button>
    </form>
</div>

</body>
</html>