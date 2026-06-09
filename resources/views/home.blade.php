@extends('layouts.app')

@section('title', 'Jornadas')
@section('meta_description', 'Próximas jornadas académicas de Sedes Sapientiae. Inscribite y participá de charlas, paneles y talleres.')

@section('css')
    <link rel="stylesheet" href="{{ url('css/home.css') }}">
@endsection

@section('header_action')
    <a id="btn-intranet" href="/admin/login" class="header-action">
        Intranet
    </a>
@endsection

@section('content')

    <section class="hero">
        <div class="container section">
            <div class="hero__inner">
                <p class="eyebrow">
                    <span></span>
                    Próximas jornadas
                </p>
                <h1>Jornadas Académicas</h1>
                <p class="hero__lead">
                    Espacios de encuentro, debate y aprendizaje organizados por la Facultad Sedes Sapientiae.
                </p>
            </div>
        </div>
    </section>

    <section class="container section">

        @if ($conferences->isEmpty())
            <div class="empty-state">
                <h2>No hay jornadas programadas</h2>
                <p>Volvé pronto, pronto habrá novedades.</p>
            </div>
        @else
            <div class="conf-grid">
                @foreach ($conferences as $conference)
                    <a
                        id="conference-{{ $conference['id'] }}"
                        href="{{ route('conferences.show', $conference['id']) }}"
                        class="conf-card"
                        aria-label="ver detalle: {{ $conference['name'] }}"
                    >
                        <div class="meta-row">
                            <span>
                                {{
                                    \Carbon\Carbon::parse($conference['date'])
                                        ->locale('es')
                                        ->isoFormat('D [de] MMMM [de] YYYY')
                                }}
                            </span>
                            <span>{{ $conference['place'] }}</span>
                        </div>

                        <h2>{{ $conference['name'] }}</h2>

                        <p class="conf-card__desc">{{ $conference['description'] }}</p>

                        <p class="conf-card__count">
                            {{ $conference['talk_count'] }} {{ $conference['talk_count'] === 1 ? 'charla' : 'charlas' }}
                        </p>
                    </a>
                @endforeach
            </div>
        @endif

    </section>

@endsection
