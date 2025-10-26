<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Consultar Doctor</title>
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/buscar.css') }}">
</head>
<body>
  <div class="container mt-4">
    <div class="consulta-modal" role="dialog" aria-modal="true">
      <div class="header">
        <div class="doctor-info">
          @php $defaultAvatar = 'https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png'; @endphp
          @if($doctor->foto_perfil)
            <img src="{{ asset('storage/profile_pics/' . $doctor->foto_perfil) }}" alt="Foto">
          @else
            <img src="{{ $defaultAvatar }}" alt="Avatar">
          @endif
          <div>
            <div style="font-weight:700">Dr(a). {{ explode(' ', trim($doctor->nombre))[0] }} {{ explode(' ', trim($doctor->apellido))[0] }}</div>
            <div style="font-size:0.95rem;color:#666">{{ $doctor->especialidad }}</div>
          </div>
        </div>
        <div>
          <a href="{{ route('mainPac') }}" class="btn btn-sm btn-link">Cerrar</a>
        </div>
      </div>

      <div class="body">
        <p style="color:#333">{{ $doctor->descripcion ?? 'Sin descripción' }}</p>

        <form method="POST" action="{{ route('consultas.store') }}">
          @csrf
          <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
          <label for="mensaje">Mensaje</label>
          <textarea id="mensaje" name="mensaje" required placeholder="Explica brevemente tu motivo y disponibilidad"></textarea>
          <div class="actions">
            <button class="btn btn-primary" type="submit">Enviar consulta</button>
            <a href="{{ route('mainPac') }}" class="btn btn-link">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
