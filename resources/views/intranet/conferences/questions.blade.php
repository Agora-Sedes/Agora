@extends('layouts.app')

@section('title', 'Gestionar preguntas · ' . $conference['title'])

@section('css')
    <link rel="stylesheet" href="{{ url('css/questions.css') }}">
@endsection

@section('header_action')
    <a href="{{ route('intranet.conferences.dashboard', ['id' => $conference['id']]) }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="container section">
    <p class="eyebrow"><span></span> Intranet · Preguntas</p>
    <h2 class="page-title" style="margin-top: 12px;">Gestionar preguntas</h2>
    <p class="muted" style="margin-bottom: 24px;">Preguntas de la conferencia <strong>{{ $conference['title'] }}</strong>.</p>

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
    <script src="{{ url('js/questions.js') }}"></script>
@endpush
