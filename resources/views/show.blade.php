@extends('app')

@section('title', $jornada['nombre'])
@section('meta_description', $jornada['descripcion'])

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
                        \Carbon\Carbon::parse($jornada['fecha'])
                            ->locale('es')
                            ->isoFormat('dddd D [de] MMMM [de] YYYY')
                    }}
                </span>
                <span>
                    {{ $jornada['lugar'] }}
                </span>
            </div>


            <h1>
                {{ $jornada['nombre'] }}
            </h1>


            @if (!empty($jornada['descripcion']))
                <p>
                    {{ $jornada['descripcion'] }}
                </p>
            @endif


            <p>
                {{ count($jornada['charlas']) }} {{ count($jornada['charlas']) === 1 ? 'charla' : 'charlas' }} en el programa
            </p>
        </div>
    </section>


    <section>

        <h2>Itinerario</h2>

        @if (empty($jornada['charlas']))
            <p>Próximamente se publicará el programa de charlas.</p>
        @else
            <div id="acordeon-charlas">

                @foreach ($jornada['charlas'] as $index => $charla)
                    <div class="charla-item" id="charla-{{ $charla['id'] }}">


                        <button
                            type="button"
                            aria-expanded="false"
                            aria-controls="charla-body-{{ $charla['id'] }}"
                            onclick="toggleCharla(this)"
                        >

                            <span>
                                {{ $index + 1 }}
                            </span>


                            <div>
                                <span>
                                    {{ $charla['titulo'] }}
                                </span>
                                <span>
                                    @if (!empty($charla['hora_inicio']))
                                        {{ $charla['hora_inicio'] }}{{ !empty($charla['hora_fin']) ? ' – ' . $charla['hora_fin'] : '' }}
                                        @if (!empty($charla['ponente'])) · @endif
                                    @endif
                                    @if (!empty($charla['ponente']))
                                        {{ $charla['ponente'] }}
                                    @endif
                                </span>
                            </div>
                        </button>


                        <div
                            id="charla-body-{{ $charla['id'] }}"
                            role="region"
                        >
                            <div>

                                @if (!empty($charla['abstract']))
                                    <p>
                                        {{ $charla['abstract'] }}
                                    </p>
                                @else
                                    <p>Abstract no disponible.</p>
                                @endif


                                <div>
                                    @if (!empty($charla['ponente']))
                                        <span>
                                            {{ $charla['ponente'] }}
                                        </span>
                                    @endif
                                    @if (!empty($charla['hora_inicio']))
                                        <span>
                                            {{ $charla['hora_inicio'] }}{{ !empty($charla['hora_fin']) ? ' – ' . $charla['hora_fin'] : '' }}
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
                id="btn-inscribirse-{{ $jornada['id'] }}"
                href="/inscripciones/{{ $jornada['id'] }}"
            >
                Inscribirme
            </a>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    /**
     * Acordeón de charlas — JS vanilla sin dependencias externas.
     * Abre/cierra individualmente; puede adaptarse a "solo uno abierto" si se desea.
     */
    function toggleCharla(btn) {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        const bodyId   = btn.getAttribute('aria-controls');
        const body     = document.getElementById(bodyId);
        const arrow    = btn.querySelector('.charla-arrow');

        btn.setAttribute('aria-expanded', String(!expanded));
        body.classList.toggle('hidden', expanded);
        arrow.style.transform = expanded ? '' : 'rotate(180deg)';
    }
</script>
@endpush
