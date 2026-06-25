@extends('layouts.app')

@section('title', 'Administrar stream')

@section('css')
    <link rel="stylesheet" href="{{ url('css/stream.css') }}">
@endsection

@section('header_action')
    <a href="{{ route('intranet.conferences.dashboard', ['id' => 1]) }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="container section">
    <p class="eyebrow"><span></span> Intranet · Streaming</p>
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
                <a href="{{ route('conferences.stream', ['id' => $conference['id']]) }}" target="_blank" class="btn btn--ghost">
                    Ver cómo se ve
                </a>
            </p>
        </div>
    @endif
</div>
@endsection
