@extends('app')

@section('title', $jornada['nombre'])
@section('meta_description', $jornada['descripcion'])

@section('header_action')
    <a
        id="btn-volver"
        href="{{ route('home') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-neutral-600"
    >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Volver
    </a>
@endsection

@section('content')


    <section class="bg-white border-b border-neutral-200">
        <div class="max-w-3xl mx-auto px-6 py-12 sm:py-16">


            <div class="flex flex-wrap items-center gap-3 mb-5">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-black bg-neutral-100 rounded-full px-3 py-1">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    {{
                        \Carbon\Carbon::parse($jornada['fecha'])
                            ->locale('es')
                            ->isoFormat('dddd D [de] MMMM [de] YYYY')
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


            <h1 class="text-3xl sm:text-4xl font-semibold text-neutral-900 tracking-tight leading-tight">
                {{ $jornada['nombre'] }}
            </h1>


            @if (!empty($jornada['descripcion']))
                <p class="mt-4 text-neutral-500 text-base leading-relaxed max-w-2xl">
                    {{ $jornada['descripcion'] }}
                </p>
            @endif


            <p class="mt-5 text-sm text-neutral-400 font-medium">
                {{ count($jornada['charlas']) }} {{ count($jornada['charlas']) === 1 ? 'charla' : 'charlas' }} en el programa
            </p>
        </div>
    </section>


    <section class="max-w-3xl mx-auto px-6 py-10">

        <h2 class="text-xs font-semibold uppercase tracking-widest text-neutral-400 mb-5">Itinerario</h2>

        @if (empty($jornada['charlas']))
            <p class="text-neutral-500 text-sm text-center py-12">Próximamente se publicará el programa de charlas.</p>
        @else
            <div class="divide-y divide-neutral-200 border border-neutral-200 rounded-2xl overflow-hidden bg-white" id="acordeon-charlas">

                @foreach ($jornada['charlas'] as $index => $charla)
                    <div class="charla-item" id="charla-{{ $charla['id'] }}">


                        <button
                            type="button"
                            class="w-full flex items-start gap-4 px-6 py-5 text-left focus:outline-none"
                            aria-expanded="false"
                            aria-controls="charla-body-{{ $charla['id'] }}"
                            onclick="toggleCharla(this)"
                        >

                            <span class="shrink-0 w-7 h-7 rounded-full bg-neutral-100 text-black text-xs font-semibold flex items-center justify-center mt-0.5">
                                {{ $index + 1 }}
                            </span>


                            <div class="flex-1 min-w-0">
                                <span class="block font-medium text-neutral-900 text-sm leading-snug">
                                    {{ $charla['titulo'] }}
                                </span>
                                <span class="block text-xs text-neutral-500 mt-1">
                                    @if (!empty($charla['hora_inicio']))
                                        {{ $charla['hora_inicio'] }}{{ !empty($charla['hora_fin']) ? ' – ' . $charla['hora_fin'] : '' }}
                                        @if (!empty($charla['ponente'])) · @endif
                                    @endif
                                    @if (!empty($charla['ponente']))
                                        {{ $charla['ponente'] }}
                                    @endif
                                </span>
                            </div>


                            <svg
                                class="shrink-0 w-5 h-5 text-neutral-400 mt-0.5 transition-transform duration-200 charla-arrow"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>


                        <div
                            id="charla-body-{{ $charla['id'] }}"
                            class="charla-body hidden px-6 pb-6 pt-1"
                            role="region"
                        >
                            <div class="ml-11">

                                @if (!empty($charla['abstract']))
                                    <p class="text-neutral-600 text-sm leading-relaxed">
                                        {{ $charla['abstract'] }}
                                    </p>
                                @else
                                    <p class="text-neutral-400 text-sm italic">Abstract no disponible.</p>
                                @endif


                                <div class="flex flex-wrap gap-2 mt-4">
                                    @if (!empty($charla['ponente']))
                                        <span class="inline-flex items-center gap-1.5 text-xs text-neutral-500 bg-neutral-100 rounded-full px-3 py-1">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/>
                                                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            </svg>
                                            {{ $charla['ponente'] }}
                                        </span>
                                    @endif
                                    @if (!empty($charla['hora_inicio']))
                                        <span class="inline-flex items-center gap-1.5 text-xs text-neutral-500 bg-neutral-100 rounded-full px-3 py-1">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                                                <path d="M12 7V12L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
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


    <section class="max-w-3xl mx-auto px-6 pb-16">
        <div class="bg-neutral-50 border border-neutral-200 rounded-2xl p-8 text-center">
            <div class="w-12 h-12 rounded-xl bg-neutral-200 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-black" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.6 9A9 9 0 1 1 9 3.4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M20 4V8H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-neutral-900 mb-2">¿Querés participar?</h3>
            <p class="text-neutral-500 text-sm mb-6 max-w-sm mx-auto leading-relaxed">
                Asegurá tu lugar en la jornada. La inscripción es gratuita y está sujeta a disponibilidad de cupos.
            </p>
            <a
                id="btn-inscribirse-{{ $jornada['id'] }}"
                href="/inscripciones/{{ $jornada['id'] }}"
                class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-black text-white font-semibold text-sm"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
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
