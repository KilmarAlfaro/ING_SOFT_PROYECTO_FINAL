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
    <div class="row">
      <div class="col-md-4">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            @if($doctor->foto_perfil)
              <img src="{{ asset('storage/profile_pics/' . $doctor->foto_perfil) }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin-right:12px;">
            @else
              <img src="{{ asset('imagenes/paciente.png') }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin-right:12px;">
            @endif
            <div>
              <h5>Dr(a). {{ explode(' ', trim($doctor->nombre))[0] }} {{ explode(' ', trim($doctor->apellido))[0] }}</h5>
              <div class="text-muted">{{ $doctor->especialidad }}</div>
            </div>
          </div>
          <hr>
          <p>{{ $doctor->descripcion ?? 'Sin descripción' }}</p>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card p-3">
          <h5>Enviar consulta</h5>
          <form method="POST" action="{{ route('consultas.store') }}">
            @csrf
            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
            <div class="mb-2">
              <textarea name="mensaje" rows="4" class="form-control" placeholder="Escribe tu mensaje..." required></textarea>
            </div>
            <div>
              <button class="btn btn-primary" type="submit">Enviar</button>
              <a href="{{ route('mainPac') }}" class="btn btn-link">Volver</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
