@extends('layouts.app')

@section('title', 'Seleccionar conferencia')

@section('content')
<div class="container section">
    <h2 class="page-title">Seleccionar conferencia</h2>

    <p style="margin: 16px 0;">
        <a href="{{ route('intranet.conferences.new') }}">Crear nueva jornada</a>
    </p>

    <div class="link-stack" style="max-width: 560px;">
        @foreach (($conferences ?? []) as $conference)
            <a href="{{ route('intranet.conferences.dashboard', ['id' => $conference->id]) }}">
                {{ $conference->title }} · {{ $conference->talks_count }} charlas
            </a>
        @endforeach
    </div>
</div>
@endsection
