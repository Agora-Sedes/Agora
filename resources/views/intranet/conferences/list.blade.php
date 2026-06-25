@extends('layouts.app')

@section('title', 'Conferencias')

@section('header_action')
    <form action="{{ route('intranet.logout') }}" method="post">
        @csrf
        <button type="submit" class="header-action">Cerrar sesión</button>
    </form>
@endsection

@section('css')
<style>
    .admin-conference-grid {
        display: grid;
        gap: 20px;
    }

    .admin-conference-card {
        display: grid;
        gap: 12px;
        padding: 22px;
        border: 1px solid #cfd4dc;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfbfb 100%);
        color: var(--text);
        text-decoration: none;
        box-shadow: 0 10px 22px rgba(27, 48, 88, 0.08);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .admin-conference-card:hover {
        transform: translateY(-2px);
        border-color: #007bff;
        box-shadow: 0 16px 30px rgba(27, 48, 88, 0.14);
        color: var(--text);
    }

    .admin-conference-card__top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .admin-conference-card__title {
        font-size: 1.45rem;
        margin: 0;
        color: var(--navy);
    }

    .admin-conference-card__desc {
        color: var(--text-muted);
        line-height: 1.55;
    }

    .admin-conference-card__badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: #eef2ff;
        color: #1b3058;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .admin-conference-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        color: var(--text-muted);
        font-size: 0.92rem;
    }

    .admin-conference-card__meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    @media (min-width: 760px) {
        .admin-conference-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endsection

@section('content')

    <section class="hero">
        <div class="container section">
            <div class="hero__inner">
                <p class="eyebrow">
                    <span></span>
                    Panel de administración
                </p>
                <h1>Seleccioná una conferencia</h1>
                <p class="hero__lead">
                    Elegí una conferencia para abrir su panel, revisar inscritos o escanear QR.
                </p>
            </div>
        </div>
    </section>

    <section class="container section">
        @if ($conferences->isEmpty())
            <div class="empty-state">
                <h2>No hay conferencias cargadas</h2>
                <p>Agregá una conferencia en la base de datos para verla acá.</p>
            </div>
        @else
            <div class="admin-conference-grid">
                @foreach ($conferences as $conference)
                    <a
                        id="conference-{{ $conference->id }}"
                        href="{{ route('intranet.conferences.dashboard', ['id' => $conference->id]) }}"
                        class="admin-conference-card"
                    >
                        <div class="admin-conference-card__top">
                            <h2 class="admin-conference-card__title">{{ $conference->title }}</h2>
                            <span class="admin-conference-card__badge">ID {{ $conference->id }}</span>
                        </div>

                        <div class="admin-conference-card__meta">
                            <span>
                                {{ $conference->starts_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                                -
                                {{ $conference->ends_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                            </span>
                        </div>

                        <p class="admin-conference-card__desc">{{ $conference->description }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

@endsection
