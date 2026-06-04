@extends('app')

@section('title', $conference['name'])
@section('meta_description', $conference['description'])

@section('header_action')
    <a
        id="btn-volver"
        href="{{ route('home') }}"
    >
        Volver
    </a>
@endsection

@section('content')


    <section>
        <div>


            <div>
                <span>
                    {{
                        \Carbon\Carbon::parse($conference['date'])
                            ->locale('es')
                            ->isoFormat('dddd D [de] MMMM [de] YYYY')
                    }}
                </span>
                <span>
                    {{ $conference['place'] }}
                </span>
            </div>


            <h1>
                {{ $conference['name'] }}
            </h1>


            @if (!empty($conference['description']))
                <p>
                    {{ $conference['description'] }}
                </p>
            @endif


            <p>
                {{ count($conference['talks']) }} {{ count($conference['talks']) === 1 ? 'talk' : 'talks' }} en el programa
            </p>
        </div>
    </section>


    <section>

        <h2>Itinerario</h2>

        @if (empty($conference['talks']))
            <p>Próximamente se publicará el programa de charlas.</p>
        @else
            <div id="acordeon-talks">

                @foreach ($conference['talks'] as $index => $talk)
                    <div class="talk-item" id="talk-{{ $talk['id'] }}">


                        <button
                            type="button"
                            aria-expanded="false"
                            aria-controls="talk-body-{{ $talk['id'] }}"
                            onclick="toggleTalk(this)"
                        >

                            <span>
                                {{ $index + 1 }}
                            </span>


                            <div>
                                <span>
                                    {{ $talk['title'] }}
                                </span>
                                <span>
                                    @if (!empty($talk['hour_begin']))
                                        {{ $talk['hour_begin'] }}{{ !empty($talk['hour_end']) ? ' – ' . $talk['hour_end'] : '' }}
                                        @if (!empty($talk['talker'])) · @endif
                                    @endif
                                    @if (!empty($talk['talker']))
                                        {{ $talk['talker'] }}
                                    @endif
                                </span>
                            </div>
                        </button>


                        <div
                            id="talk-body-{{ $talk['id'] }}"
                            role="region"
                        >
                            <div>

                                @if (!empty($talk['abstract']))
                                    <p>
                                        {{ $talk['abstract'] }}
                                    </p>
                                @else
                                    <p>Abstract no disponible.</p>
                                @endif


                                <div>
                                    @if (!empty($talk['talker']))
                                        <span>
                                            {{ $talk['talker'] }}
                                        </span>
                                    @endif
                                    @if (!empty($talk['hour_begin']))
                                        <span>
                                            {{ $talk['hour_begin'] }}{{ !empty($talk['hour_end']) ? ' – ' . $talk['hour_end'] : '' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        @endif

    </section>


    <section>
        <div>
            <h3>¿Querés participar?</h3>
            <p>
                Asegurá tu lugar en la jornada. La inscripción es gratuita y está sujeta a disponibilidad de cupos.
            </p>
            <a
                id="btn-inscription-{{ $conference['id'] }}"
                href="/inscriptions/{{ $conference['id'] }}"
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
