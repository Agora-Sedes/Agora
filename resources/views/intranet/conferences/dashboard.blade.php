@extends('layouts.app')

@section('title', 'Panel de administración')

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

    <div class="link-stack" style="max-width: 560px;">
        <a href="{{ route('intranet.conferences.qr-scan', [ 'id' => 1 ]) }}">Escanear QR</a>
        <a href="{{ route('intranet.conferences.attendants.list', [ 'id' => 1 ]) }}">Inscritos</a>
        <a href="{{ route('intranet.conferences.attendants.new', [ 'id' => 1 ]) }}">Agregar inscripto de último momento</a>
        <a href="{{ route('intranet.conferences.stream.edit', [ 'id' => 1 ]) }}">Administrar stream</a>
    </div>
</div>
@endsection
