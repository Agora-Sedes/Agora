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

        @if ($jornadas->isEmpty())

            <div>
                <h2>No hay jornadas programadas</h2>
                <p>Volvé pronto, pronto habrá novedades.</p>
            </div>
        @else
            <div>
                @foreach ($jornadas as $jornada)
                    <a
                        id="jornada-{{ $jornada['id'] }}"
                        href="{{ route('conferences.show', $jornada['id']) }}"
                        aria-label="ver detalle: {{ $jornada['nombre'] }}"
                    >
                        <div>


                            <div>

                                <div>
                                    <span>
                                        {{
                                            \Carbon\Carbon::parse($jornada['fecha'])
                                                ->locale('es')
                                                ->isoFormat('D [de] MMMM [de] YYYY')
                                        }}
                                    </span>
                                    <span>
                                        {{ $jornada['lugar'] }}
                                    </span>
                                </div>


                                <h2>
                                    {{ $jornada['nombre'] }}
                                </h2>


                                <p>
                                    {{ $jornada['descripcion'] }}
                                </p>


                                <p>
                                    {{ $jornada['charlas_count'] }} {{ $jornada['charlas_count'] === 1 ? 'charla' : 'charlas' }}
                                </p>
                            </div>

                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </section>

@endsection
