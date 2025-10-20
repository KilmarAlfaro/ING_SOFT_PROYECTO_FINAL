@extends('layout')

@section('content')
  <div class="container">
    <h1>Consultas recibidas</h1>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @forelse($consultas as $c)
      <div class="consulta-card">
        <div class="meta">
          <strong>{{ $c->paciente->nombre }} {{ $c->paciente->apellido }}</strong>
          <span class="fecha">{{ $c->created_at->format('d-m-Y H:i') }}</span>
        </div>
        <p class="mensaje">{{ $c->mensaje }}</p>
        @if($c->respuesta)
          <div class="respuesta">Respuesta: {{ $c->respuesta }}</div>
        @else
          <form action="{{ route('consultas.responder', $c) }}" method="POST">
            @csrf
            <textarea name="respuesta" rows="3" required placeholder="Escribe tu respuesta..."></textarea>
            <button type="submit" class="btn btn-primario">Responder</button>
          </form>
        @endif
      </div>
    @empty
      <p>No tiene consultas nuevas.</p>
    @endforelse
  </div>
@endsection
