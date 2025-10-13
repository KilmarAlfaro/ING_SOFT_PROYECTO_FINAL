<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mainPac</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <div class="nav-right">
            <!-- Botón Cerrar Sesión -->
            <button type="button" class="logout-btn" onclick="openModal()">Cerrar sesión</button>

            <a href="{{ route('perfil.paciente') }}">
                <img src="https://cdn4.iconfinder.com/data/icons/glyphs/24/icons_user2-64.png" alt="Perfil">
            </a>
    </div>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Buscar Doctores</h1>
    </div>

    <form method="GET" action="{{ route('buscar.doctor') }}" class="input-group mb-3">
        <input type="text" name="query" class="form-control" placeholder="Buscar doctor por nombre o especialidad">
        <button class="btn btn-primary" type="submit">Buscar</button>
    </form>

    <div class="row">
        @if(isset($doctores) && count($doctores) > 0)
            @foreach($doctores as $doctor)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $doctor->nombre }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $doctor->especialidad }}</h6>
                            <p class="card-text">{{ $doctor->descripcion }}</p>
                            <a href="{{ route('consulta.doctor', $doctor->id) }}" class="btn btn-success">Hacer Consulta</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-center">No se encontraron doctores.</p>
        @endif
    </div>




</body>
</html>