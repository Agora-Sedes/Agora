@extends('app')

@section('title', 'Jornadas')
@section('meta_description', 'Próximas jornadas académicas de Sedes Sapientiae. Inscribite y participá de charlas, paneles y talleres.')


@section('header_action')
    <a
        id="btn-intranet"
        href="/admin/login"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-black text-white"
    >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M15 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 17L15 12L10 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Intranet
    </a>
@endsection

@section('content')


    <section class="bg-white border-b border-neutral-200">
        <div class="max-w-5xl mx-auto px-6 py-14 sm:py-20 text-center">
            <p class="inline-flex items-center gap-2 text-black text-xs font-semibold uppercase tracking-widest mb-4">
                <span class="w-5 h-px bg-neutral-400 inline-block"></span>
                Próximas jornadas
                <span class="w-5 h-px bg-neutral-400 inline-block"></span>
            </p>
            <h1 class="text-4xl sm:text-5xl font-semibold text-neutral-900 tracking-tight leading-tight">
                Jornadas Académicas
            </h1>
            <p class="mt-4 text-neutral-500 text-lg max-w-xl mx-auto leading-relaxed">
                Espacios de encuentro, debate y aprendizaje organizados por la Facultad Sedes Sapientiae.
            </p>
        </div>
    </section>


    <section class="max-w-5xl mx-auto px-6 py-12">

        @if ($jornadas->isEmpty())

            <div class="text-center py-24">
                <div class="w-16 h-16 rounded-2xl bg-neutral-100 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-neutral-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-neutral-700 mb-2">No hay jornadas programadas</h2>
                <p class="text-neutral-500 text-sm">Volvé pronto, pronto habrá novedades.</p>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-1">
                @foreach ($jornadas as $jornada)
                    <a
                        id="jornada-{{ $jornada['id'] }}"
                        href="{{ route('conferences.show', $jornada['id']) }}"
                        class="block bg-white border border-neutral-200 rounded-2xl p-7 sm:p-8"
                        aria-label="Ver detalle: {{ $jornada['nombre'] }}"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">


                            <div class="flex-1 min-w-0">

                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-black bg-neutral-100 rounded-full px-3 py-1">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                        {{
                                            \Carbon\Carbon::parse($jornada['fecha'])
                                                ->locale('es')
                                                ->isoFormat('D [de] MMMM [de] YYYY')
                                        }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-neutral-500 bg-neutral-100 rounded-full px-3 py-1">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7Z" stroke="currentColor" stroke-width="1.5"/>
                                            <circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        {{ $jornada['lugar'] }}
                                    </span>
                                </div>


                                <h2 class="text-xl font-semibold text-neutral-900 leading-snug">
                                    {{ $jornada['nombre'] }}
                                </h2>


                                <p class="mt-2 text-neutral-500 text-sm leading-relaxed line-clamp-2">
                                    {{ $jornada['descripcion'] }}
                                </p>


                                <p class="mt-3 text-xs text-neutral-400 font-medium">
                                    {{ $jornada['charlas_count'] }} {{ $jornada['charlas_count'] === 1 ? 'charla' : 'charlas' }}
                                </p>
                            </div>


                            <div class="shrink-0 self-center">
                                <div class="w-10 h-10 rounded-full border border-neutral-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-neutral-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>

                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </section>

@endsection
