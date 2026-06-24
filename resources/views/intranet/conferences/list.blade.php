@extends('layouts.app')

@section('title', 'Conferencias')

@section('header_action')
    <form action="{{ route('intranet.logout') }}" method="post">
        @csrf
        <button type="submit" class="header-action">Cerrar sesión</button>
    </form>
@endsection

@section('content')
<div class="container section">
    <p class="eyebrow"><span></span> Intranet</p>
    <h2 class="page-title" style="margin-top: 12px;">Conferencias</h2>
    <p class="muted" style="margin-top: 8px;">Seleccioná una conferencia para entrar a su panel de administración.</p>

    @if ($conferences->isEmpty())
        <div class="empty-state" style="margin-top: 24px;">
            <h2>No hay conferencias cargadas</h2>
            <p>Agregá una conferencia en la base de datos para verla acá.</p>
        </div>
    @else
        <div class="conf-grid" style="margin-top: 24px;">
            @foreach ($conferences as $conference)
                <a
                    id="conference-{{ $conference->id }}"
                    href="{{ route('intranet.conferences.dashboard', ['id' => $conference->id]) }}"
                    class="conf-card"
                >
                    <span class="meta-row">
                        {{ $conference->starts_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                        -
                        {{ $conference->ends_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                    </span>

                    <h2>{{ $conference->title }}</h2>

                    <p class="conf-card__desc">{{ $conference->description }}</p>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
