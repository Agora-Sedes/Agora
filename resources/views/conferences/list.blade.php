@extends('layouts.app')

@section('title', 'Jornadas')
@section('meta_description', 'Próximas jornadas del Instituto de Profesorado Sedes Sapientiae.')

@section('css')
    <link rel="stylesheet" href="{{ url('css/home.css') }}">
@endsection

@section('header_action')
    <a id="btn-intranet" href="{{ route('intranet.login') }}" class="header-action">
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
                <h1>Jornadas de Innovación y Práctica Docente</h1>
                <p class="hero__lead">
                    Espacios de encuentro, debate y aprendizaje organizados por el Instituto de Profesorado Sedes Sapientiae.
                </p>
            </div>
        </div>
    </section>

    <section class="container section">

        @if ($conferences->isEmpty())
            <div class="empty-state">
                <h2>No hay jornadas programadas</h2>
                <p>Volvé pronto para ver las novedades.</p>
            </div>
        @else
            <div class="conf-grid">
                @foreach ($conferences as $conference)
                    <a
                        id="conference-{{ $conference['id'] }}"
                        href="{{ route('conferences.show', $conference['id']) }}"
                        class="conf-card"
                    >
                        <span class="meta-row">
                            {{
                                \Carbon\Carbon::parse($conference['starts_at'])
                                    ->locale('es')
                                    ->isoFormat('D [de] MMMM [de] YYYY')
                            }}
                            -
                            {{
                                \Carbon\Carbon::parse($conference['ends_at'])
                                    ->locale('es')
                                    ->isoFormat('D [de] MMMM [de] YYYY')
                            }}
                        </span>

                        <h2>{{ $conference['title'] }}</h2>

                        <p class="conf-card__desc">{{ $conference['description'] }}</p>
                    </a>
                @endforeach
            </div>
        @endif

    </section>

@endsection
