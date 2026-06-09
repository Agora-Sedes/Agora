@extends('layouts.app')

@section('title', 'Error en el pago')

@section('content')
<div class="center-page">
    <div class="panel cta">
        <h1>Hubo un problema con tu pago</h1>
        <p>No se pudo procesar tu pago. Podés intentarlo nuevamente.</p>
        <a href="/buy" class="btn btn--primary">Volver a intentar</a>
    </div>
</div>
@endsection
