@extends('layouts.app')

@section('title', 'Inscripción recibida')

@section('css')
    <link rel="stylesheet" href="{{ url('css/inscription-confirmation.css') }}">
@endsection

@section('content')
<div class="container section">
    <div class="panel" style="max-width: 640px; margin-inline: auto;">
        <p class="eyebrow"><span></span> Confirmación de inscripción</p>
        <h1 class="page-title" style="margin-top: 12px;">Inscripción completada con éxito</h1>
        <p class="muted">Se registraron {{ count($participants) }} participante(s):</p>

        <div class="stack" style="margin-top: 24px;">
            @foreach ($participants as $index => $participant)
                <div class="card" style="padding: 20px;">
                    <h3 style="margin-bottom: 12px;">Persona {{ $index + 1 }}</h3>
                    <table class="confirmation-list">
                        <tr><td class="muted">Nombre</td><td>{{ $participant['name'] }}</td></tr>
                        <tr><td class="muted">Apellido</td><td>{{ $participant['lastname'] }}</td></tr>
                        <tr><td class="muted">DNI</td><td>{{ $participant['dni'] }}</td></tr>
                        <tr><td class="muted">Email</td><td>{{ $participant['email'] }}</td></tr>
                        <tr><td class="muted">Teléfono</td><td>{{ $participant['phone'] }}</td></tr>
                    </table>
                </div>
            @endforeach
        </div>

        <a href="{{ route('conferences.list') }}" class="btn btn--ghost" style="margin-top: 24px;">Volver al inicio</a>
    </div>
</div>
@endsection
