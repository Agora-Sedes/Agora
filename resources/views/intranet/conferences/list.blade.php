@extends('layouts.app')

@section('title', 'Seleccionar conferencia')

@section('content')
<div class="container section">
    <h2 class="page-title">Seleccionar conferencia</h2>

    <div class="link-stack" style="max-width: 560px;">
        @foreach ($conferences as $conference)
            <a href="{{ route('intranet.conferences.dashboard', ['id' => $conference->id]) }}">
                {{ $conference->title }}
                <small class="muted"> · ${{ number_format($conference->price, 0, ',', '.') }}</small>
            </a>
        @endforeach
    </div>
</div>
@endsection
