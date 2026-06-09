@extends('layouts.app')

@section('title', 'Agregar inscripto')

@section('header_action')
    <a href="{{ route('dashboard') }}" class="header-action">← Panel</a>
@endsection

@section('content')
<div class="center-page">
    <div class="panel">
        <h2 class="page-title">Agregar inscripto de último momento</h2>

        <form action="" method="post" class="stack">
            @csrf
            <div class="field">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" class="input" required>
            </div>
            <div class="field">
                <label for="lastname">Apellido</label>
                <input type="text" id="lastname" name="lastname" class="input" required>
            </div>
            <div class="field">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" class="input" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="input" required>
            </div>
            <div class="field">
                <label for="phone">Teléfono</label>
                <input type="text" id="phone" name="phone" class="input" required>
            </div>
            <button type="submit" class="btn btn--primary btn--block">Agregar inscripto</button>
        </form>
    </div>
</div>
@endsection
