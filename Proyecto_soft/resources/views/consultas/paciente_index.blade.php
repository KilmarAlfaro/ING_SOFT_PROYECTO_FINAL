@extends('layout')

@section('content')
  <div class="container">
    <h1>Mis consultas</h1>
    @forelse($consultas as $c)
      <div class="consulta-card">
        <div class="meta">
          <strong>Dr(a). {{ $c->doctor->nombre }} {{ $c->doctor->apellido }}</strong>
          <span class="fecha">{{ $c->created_at->format('d-m-Y H:i') }}</span>
        </div>
        <p class="mensaje">{{ $c->mensaje }}</p>
        @if($c->respuesta)
          <div class="respuesta">Respuesta del doctor: {{ $c->respuesta }}</div>
        @else
          <div class="estado">Estado: {{ $c->status }}</div>
        @endif
      </div>
    @empty
      <p>No has enviado consultas aún.</p>
    @endforelse
  </div>
@endsection
