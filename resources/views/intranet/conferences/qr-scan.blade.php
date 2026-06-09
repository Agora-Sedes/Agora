@extends('layouts.app')

@section('title', 'Escanear QR')

@section('header_action')
    <a href="{{ route('intranet.conferences.dashboard', [ 'id' => 1 ]) }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="container section">
    <h2 class="page-title">Escanear QR</h2>

    <div class="empty-state card" style="padding: 48px 24px;">
        <h2>Lector de QR</h2>
        <p>Acá se mostrará el escáner de códigos QR.</p>
    </div>
</div>
@endsection
