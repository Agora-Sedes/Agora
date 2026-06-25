@extends('layouts.app')

@section('title', 'Panel de administracion')

@section('header_action')
    <form action="{{ route('intranet.logout') }}" method="post">
        @csrf
        <button type="submit" class="header-action">Cerrar sesión</button>
    </form>
@endsection

@section('content')
<div class="container section">
    <p class="eyebrow"><span></span> Intranet</p>
    <h2 class="page-title" style="margin-top: 12px;">Panel de administración</h2>
    <p class="muted" style="margin-top: 8px;">{{ $conference->title }}</p>

    <div class="link-stack" style="max-width: 560px;">
        <a href="{{ route('intranet.conferences.qr-scan', [ 'id' => $conference->id ]) }}">Escanear QR</a>
        <a href="{{ route('intranet.conferences.attendants.list', [ 'id' => $conference->id ]) }}">Inscritos</a>
        <a href="{{ route('intranet.conferences.attendants.new', [ 'id' => $conference->id ]) }}">Agregar inscripto de último momento</a>
        <a href="{{ route('intranet.conferences.stream.edit', [ 'id' => $conference->id ]) }}">Administrar stream</a>
    </div>
</div>
@endsection
