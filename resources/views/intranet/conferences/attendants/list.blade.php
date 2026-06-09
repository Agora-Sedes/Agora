@extends('layouts.app')

@section('title', 'Inscritos')

@section('header_action')
    <a href="{{ route('dashboard') }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="container section">
    <h2 class="page-title">Inscritos</h2>

    {{-- It always seems impossible until it is done. - Nelson Mandela --}}
    <div class="empty-state card" style="padding: 48px 24px;">
        <h2>Sin datos por ahora</h2>
        <p>El listado de inscritos se mostrará acá.</p>
    </div>
</div>
@endsection
