@extends('layouts.app')

@section('title', 'Stream · ' . $conference['title'])
@section('meta_description', $conference['description'])

@section('css')
    <link rel="stylesheet" href="{{ url('css/stream.css') }}">
@endsection

@section('header_action')
    <a id="btn-volver" href="{{ route('conferences.show', ['id' => $conference['id']]) }}" class="header-action">
        ← Volver
    </a>
@endsection

@section('content')

<section class="container section section--tight">
    <div class="stream-stage @if (!empty($conference['youtube_id'])) stream-stage--live @else stream-stage--empty @endif">

        <div class="stream-stage__frame">
            @if (!empty($conference['youtube_id']))
                <div id="frame-container" data-video-id="{{ $conference['youtube_id'] }}" data-conference-id="{{ $conference['id'] }}"></div>
                <div class="stream-stage__overlay hidden" id="ended-overlay">
                    <div class="stream-stage__overlay-icon" aria-hidden="true">●</div>
                    <h2>Transmisión finalizada</h2>
                    <p>Gracias por acompañarnos</p>
                </div>
            @else
                <div class="stream-stage__placeholder">
                    <p class="eyebrow"><span></span> Sin transmisión</p>
                    <h2 style="margin-top: 12px;">Aún no hay una transmisión configurada.</h2>
                    <p class="muted" style="margin-top: 12px;">El administrador debe configurar el video de YouTube desde la intranet.</p>
                </div>
            @endif
        </div>

        <div class="stream-stage__status">
            <div class="stream-stage__info">
                <span class="meta-row">
                    {{
                        \Carbon\Carbon::parse($conference['starts_at'])
                            ->locale('es')
                            ->isoFormat('D [de] MMMM [de] YYYY')
                    }}
                </span>
                <span class="stream-stage__title">{{ $conference['title'] }}</span>
            </div>

            @if (!empty($conference['youtube_id']))
                <div class="stream-stage__controls">
                    <div class="stream-volume" id="volume-control">
                        <svg class="stream-volume__icon" id="vol-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><line x1="23" y1="9" x2="17" y2="15" /><line x1="17" y1="9" x2="23" y2="15" />
                        </svg>
                        <input type="range" min="0" max="100" value="0" class="stream-volume__slider" id="vol-slider" aria-label="Volumen" />
                    </div>

                    <span class="stream-live-badge" id="live-badge">
                        <span class="stream-live-badge__dot" aria-hidden="true"></span>
                        <span id="badge-text">EN VIVO</span>
                        <span class="stream-live-badge__signal" aria-hidden="true">
                            <span></span><span></span><span></span><span></span>
                        </span>
                    </span>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script>
        window.__VIDEO_ID__ = @json($conference['youtube_id'] ?? null);
        window.__CONFERENCE_ID__ = {{ (int) $conference['id'] }};
    </script>
    <script src="{{ url('js/stream.js') }}"></script>
@endpush
