@extends('layouts.app')

@section('title', 'Seleccionar conferencia')

@section('content')
<div class="container section">
    <h2 class="page-title">Seleccionar conferencia</h2>

    @php $conferences = [["name" => "Conferencia 1"], ["name" => "Conferencia 2"]]; @endphp

    <div class="link-stack" style="max-width: 560px;">
        @foreach ($conferences as $conference)
            <a href="{{ route('dashboard', ['name' => $conference['name']]) }}">{{ $conference['name'] }}</a>
        @endforeach
    </div>
</div>
@endsection
