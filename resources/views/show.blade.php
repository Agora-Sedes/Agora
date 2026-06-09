@extends('layouts.app')

@section('title', $conference['name'])
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
                <div class="meta-row">
                    <span>
                        {{
                            \Carbon\Carbon::parse($conference['date'])
                                ->locale('es')
                                ->isoFormat('dddd D [de] MMMM [de] YYYY')
                        }}
                    </span>
                    <span>{{ $conference['place'] }}</span>
                </div>

                <h1>{{ $conference['name'] }}</h1>

                @if (!empty($conference['description']))
                    <p class="hero__lead">{{ $conference['description'] }}</p>
                @endif

                <p class="badge">
                    {{ count($conference['talks']) }} {{ count($conference['talks']) === 1 ? 'charla' : 'charlas' }} en el programa
                </p>
            </div>
        </div>
    </section>

    <section class="container section">

        <h2 class="page-title">Itinerario</h2>

        @if (empty($conference['talks']))
            <p class="muted">Próximamente se publicará el programa de charlas.</p>
        @else
            <div id="acordeon-talks" class="accordion">

                @foreach ($conference['talks'] as $index => $talk)
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
                                    @if (!empty($talk['hour_begin']))
                                        {{ $talk['hour_begin'] }}{{ !empty($talk['hour_end']) ? ' – ' . $talk['hour_end'] : '' }}
                                        @if (!empty($talk['talker'])) · @endif
                                    @endif
                                    @if (!empty($talk['talker']))
                                        {{ $talk['talker'] }}
                                    @endif
                                </span>
                            </span>
                        </button>

                        <div id="talk-body-{{ $talk['id'] }}" class="talk-item__body" role="region">
                            @if (!empty($talk['abstract']))
                                <p>{{ $talk['abstract'] }}</p>
                            @else
                                <p>Abstract no disponible.</p>
                            @endif

                            <div class="meta-row">
                                @if (!empty($talk['talker']))
                                    <span>{{ $talk['talker'] }}</span>
                                @endif
                                @if (!empty($talk['hour_begin']))
                                    <span>
                                        {{ $talk['hour_begin'] }}{{ !empty($talk['hour_end']) ? ' – ' . $talk['hour_end'] : '' }}
                                    </span>
                                @endif
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
                Asegurá tu lugar en la jornada. La inscripción es gratuita y está sujeta a disponibilidad de cupos.
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
