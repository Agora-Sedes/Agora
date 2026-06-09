@extends('layouts.app')

@section('title', 'Panel de administración')

@section('header_action')
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <button type="submit" class="header-action">Cerrar sesión</button>
    </form>
@endsection

@section('content')
<div class="container section">
    <p class="eyebrow"><span></span> Intranet</p>
    <h2 class="page-title" style="margin-top: 12px;">Panel de administración</h2>

    <div class="link-stack" style="max-width: 560px;">
        <a href="{{ route('qrscan') }}">Escanear QR</a>
        <a href="{{ route('attendants') }}">Inscritos</a>
        <a href="{{ route('addattendant') }}">Agregar inscripto de último momento</a>
    </div>
</div>
@endsection
