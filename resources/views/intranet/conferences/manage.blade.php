@extends('layouts.app')

@section('title', 'Administrar transmisión · ' . $conference['title'])

@section('css')
    <link rel="stylesheet" href="{{ url('css/stream.css') }}">
    <link rel="stylesheet" href="{{ url('css/questions.css') }}">
@endsection

@section('header_action')
    <a href="{{ route('intranet.conferences.dashboard', ['id' => $conference['id']]) }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="container section">
    <p class="eyebrow"><span></span> Intranet · Transmisión y preguntas</p>
    <h2 class="page-title" style="margin-top: 12px;">Administrar transmisión en vivo</h2>
    <p class="muted" style="margin-bottom: 24px;">Configurá el link de YouTube de la conferencia <strong>{{ $conference['title'] }}</strong>.</p>

    @if (session('status'))
        <div class="status-message">{{ session('status') }}</div>
    @endif

    <div class="panel" style="max-width: 640px;">
        <form action="{{ route('intranet.conferences.stream.update', ['id' => $conference['id']]) }}" method="POST" class="stack">
            @csrf

            <div class="field">
                <label for="youtube_link">Link de YouTube</label>
                <input
                    type="text"
                    name="youtube_link"
                    id="youtube_link"
                    class="input"
                    placeholder="https://www.youtube.com/watch?v=... o el ID del video"
                    value="{{ old('youtube_link', $conference['youtube_id']) }}"
                    required
                >
                @error('youtube_link')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <small class="muted">Pegá el link completo o únicamente el ID del video (11 caracteres).</small>
            </div>

            <div class="stream-actions">
                <button type="submit" class="btn btn--primary">Guardar transmisión</button>

                @if (!empty($conference['youtube_id']))
                    <button type="submit" formaction="{{ route('intranet.conferences.stream.stop', ['id' => $conference['id']]) }}" class="btn btn--ghost">Detener transmisión</button>
                @endif
            </div>
        </form>
    </div>

    @if (!empty($conference['youtube_id']))
        <div class="card" style="max-width: 640px; margin-top: 24px;">
            <p class="eyebrow"><span></span> Stream activo</p>
            <p style="margin-top: 12px;">
                <code class="stream-active-id">{{ $conference['youtube_id'] }}</code>
            </p>
            <p style="margin-top: 12px;">
                <a href="{{ route('intranet.conferences.stream.preview', ['id' => $conference['id']]) }}" target="_blank" class="btn btn--ghost">
                    Ver cómo se ve
                </a>
            </p>
        </div>
    @endif

    <hr style="margin: 32px 0; border: none; border-top: 1px solid var(--border);">

    <div id="questions-admin" class="questions-panel">
        <div class="questions-header">
            <h2>Todas las preguntas</h2>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="questions-count">0</span>
                <button class="questions-refresh">Actualizar</button>
            </div>
        </div>

        <p class="questions-notice" style="display: none;"></p>

        <ul class="questions-admin-list" aria-live="polite">
            <li class="question-empty">Cargando…</li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.__CONFERENCE_ID__ = {{ (int) $conference['id'] }};
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="{{ url('js/questions.js') }}"></script>
@endpush
