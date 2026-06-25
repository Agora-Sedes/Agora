@extends('layouts.app')

@section('title', 'MercadoPago callback')

@section('content')
<div class="container section">
    <div class="panel" style="max-width: 640px; margin-inline: auto;">
        <p class="eyebrow"><span></span> Pago</p>
        <h1 class="page-title" style="margin-top: 12px;">Resultado del pago</h1>

        <p>Estado: <strong>{{ $status ?? 'desconocido' }}</strong></p>
        <p>ID de pago: {{ $payment_id ?? '-' }}</p>

        <a href="{{ route('conferences.list') }}" class="btn btn--ghost" style="margin-top: 24px;">Volver al inicio</a>
    </div>
</div>
@endsection
