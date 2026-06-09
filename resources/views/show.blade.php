@extends('layouts.app')

@section('title', $conference['title'])
@section('meta_description', $conference['description'])

@section('css')
    <link rel="stylesheet" href="{{ url('css/show.css') }}">
@endsection

@section('header_action')
    <a id="btn-volver" href="{{ route('home') }}" class="header-action">
        ← Volver
    </a>
@endsection

@section('content')
    <section class="hero">
        <div class="container section">
            <div class="hero__inner">
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

                <h1>{{ $conference['title'] }}</h1>

                @if (!empty($conference['description']))
                    <p class="hero__lead">{{ $conference['description'] }}</p>
                @endif

                <p class="badge">
                    {{ $talks->count() }} {{ $talks->count() === 1 ? 'charla' : 'charlas' }} en el itinerario
                </p>
            </div>
        </div>
    </section>

    <section class="container section">

        <h2 class="page-title">Itinerario</h2>

        @if ($talks->isEmpty())
            <p class="muted">Próximamente se publicará el programa de charlas.</p>
        @else
            <div class="accordion">

                @foreach ($talks as $index => $talk)
                    <div class="talk-item" id="talk-{{ $talk['id'] }}">

                        <button
                            type="button"
                            class="talk-item__header"
                            aria-expanded="false"
                            aria-controls="talk-body-{{ $talk['id'] }}"
                            onclick="toggleTalk(this)"
                        >
                            <span class="talk-item__num">{{ $index + 1 }}</span>

                            <span class="talk-item__titles">
                                <span class="talk-item__title">{{ $talk['title'] }}</span>
                                <span class="talk-item__sub">
                                    {{
                                        \Carbon\Carbon::parse($talk['starts_at'])
                                            ->locale('es')
                                            ->isoFormat('D [de] MMMM [de] YYYY')
                                    }}
                                    -
                                    {{
                                        \Carbon\Carbon::parse($talk['ends_at'])
                                            ->locale('es')
                                            ->isoFormat('D [de] MMMM [de] YYYY')
                                    }}
                                    ·
                                    {{ $talk['speaker'] }}
                                </span>
                            </span>
                        </button>

                        <div id="talk-body-{{ $talk['id'] }}" class="talk-item__body" role="region">
                            @if (!empty($talk['description']))
                                <p>{{ $talk['description'] }}</p>
                            @else
                                <p>Descripción no disponible.</p>
                            @endif

                            <div class="meta-row">
                                <p class="talk-speaker"> {{ $talk['speaker'] }} </p>
                                -
                                <p class="talk-speaker-background">
                                    {{ $talk['speaker_background'] }}
                                </p>
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        @endif

    </section>

    <section class="container section--tight">
        <div class="panel cta">
            <h3>¿Querés participar?</h3>
            <p>
                Asegurá tu lugar en la jornada.
                <br>
                La inscripción es paga y está sujeta a disponibilidad de cupos.
            </p>
            <a
                id="btn-inscription-{{ $conference['id'] }}"
                href="/inscriptions/{{ $conference['id'] }}"
                class="btn btn--primary"
            >
                Inscribirme
            </a>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function toggleTalk(btn) {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        const bodyId   = btn.getAttribute('aria-controls');
        const body     = document.getElementById(bodyId);

        btn.setAttribute('aria-expanded', String(!expanded));
        body.style.display = expanded ? 'none' : 'block';
    }
</script>
@endpush
