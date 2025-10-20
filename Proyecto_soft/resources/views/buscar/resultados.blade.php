<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buscar doctores</title>
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/buscar.css') }}">
</head>
<body>
  <div class="search-container layout-right">
    <div class="left-panel">
      <h2>Buscar doctores</h2>
      <form method="GET" action="{{ route('buscar.doctor') }}" class="search-form">
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Buscar por nombre">
        <select name="especialidad">
          <option value="">Todas las especialidades</option>
          @php
            $especialidades = ['General','Cardiologo','Cirujano plastico','Pediatra','Dermatologo','Ginecologo','Neurologo','Ortopedista','Oftalmologo','Psiquiatra','Otro'];
          @endphp
          @foreach($especialidades as $esp)
            <option value="{{ $esp }}" {{ (isset($especialidad) && $especialidad == $esp) ? 'selected' : '' }}>{{ $esp }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primario">Buscar</button>
      </form>

      <p class="help">Filtre por especialidad o nombre. Haga clic en un doctor para ver más detalles a la derecha.</p>
    </div>

    <aside class="results-panel">
      @if($doctores->count() === 0)
        <div class="empty-results">
          <p>No se encontraron doctores que coincidan.</p>
          <p>Pruebe con otra especialidad o parte del nombre.</p>
        </div>
      @else
        <ul class="doctor-list">
          @foreach($doctores as $doc)
            @php
              $firstName = explode(' ', trim($doc->nombre))[0] ?? $doc->nombre;
              $firstLast = explode(' ', trim($doc->apellido))[0] ?? $doc->apellido;
            @endphp
            <li class="doctor-item" data-id="{{ $doc->id }}" data-nombre="{{ $firstName }} {{ $firstLast }}">
              <div class="left">
                @if($doc->foto_perfil)
                  <img src="{{ asset('storage/profile_pics/' . $doc->foto_perfil) }}" alt="{{ $doc->nombre }}">
                @else
                  <img src="{{ asset('imagenes/paciente.png') }}" alt="avatar">
                @endif
              </div>
              <div class="mid">
                <strong>Dr(a). {{ $firstName }} {{ $firstLast }}</strong>
                <div class="especialidad">{{ $doc->especialidad }}</div>
                <div class="desc">{{ \Illuminate\Support\Str::limit($doc->descripcion ?? 'Sin descripción', 120) }}</div>
              </div>
              <div class="right">
                <button class="btn ver-detalle" data-id="{{ $doc->id }}">Ver</button>
              </div>
            </li>
          @endforeach
        </ul>
        <div class="pagination">{{ $doctores->links() }}</div>
      @endif
    </aside>
  </div>

  <script>
    // When user clicks Ver, scroll to open the detail panel in a new tab or to the mainDoc URL with query param
    document.querySelectorAll('.ver-detalle').forEach(function(btn){
      btn.addEventListener('click', function(e){
        var id = this.dataset.id;
        // Redirect to consulta page for doctor where patient can message (or mainDoc where doctor sees incoming queries)
        // For now redirect to /consulta-doctor/{id}
        window.location.href = '/consulta-doctor/' + id;
      });
    });
  </script>
</body>
</html>
