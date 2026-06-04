@extends('app')

@section('title', 'Jornadas')
@section('meta_description', 'Próximas jornadas académicas de Sedes Sapientiae. Inscribite y participá de charlas, paneles y talleres.')


@section('header_action')
    <a
        id="btn-intranet"
        href="/admin/login"
        >
        Intranet
    </a>
@endsection

@section('content')


    <section>
        <div>
            <p>
                <span></span>
                Próximas jornadas
                <span></span>
            </p>
            <h1>
                Jornadas Académicas
            </h1>
            <p>
                Espacios de encuentro, debate y aprendizaje organizados por la Facultad Sedes Sapientiae.
            </p>
        </div>
    </section>


    <section>

        @if ($conferences->isEmpty())

            <div>
                <h2>No hay jornadas programadas</h2>
                <p>Volvé pronto, pronto habrá novedades.</p>
            </div>
        @else
            <div>
                @foreach ($conferences as $conference)
                    <a
                        id="conference-{{ $conference['id'] }}"
                        href="{{ route('conferences.show', $conference['id']) }}"
                        aria-label="ver detalle: {{ $conference['name'] }}"
                    >
                        <div>


                            <div>

                                <div>
                                    <span>
                                        {{
                                            \Carbon\Carbon::parse($conference['date'])
                                                ->locale('es')
                                                ->isoFormat('D [de] MMMM [de] YYYY')
                                        }}
                                    </span>
                                    <span>
                                        {{ $conference['place'] }}
                                    </span>
                                </div>


                                <h2>
                                    {{ $conference['name'] }}
                                </h2>


                                <p>
                                    {{ $conference['description'] }}
                                </p>


                                <p>
                                    {{ $conference['talk_count'] }} {{ $conference['talk_count'] === 1 ? 'talk' : 'talk' }}
                                </p>
                            </div>

                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </section>

@endsection
