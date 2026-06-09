@extends('layouts.app')

@section('title', 'Escanear QR')

@section('header_action')
    <a href="{{ route('dashboard') }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="container section">
    <h2 class="page-title">Escanear QR</h2>

    {{-- When there is no desire, all things are at peace. - Laozi --}}
    <div class="empty-state card" style="padding: 48px 24px;">
        <h2>Lector de QR</h2>
        <p>Acá se mostrará el escáner de códigos QR.</p>
    </div>
</div>
@endsection
