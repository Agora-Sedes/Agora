@extends('layouts.app')

@section('title', 'Acceso administrador')

@section('content')
<div class="center-page">
    <div class="panel">
        <p class="eyebrow"><span></span> Intranet</p>
        <h2 class="page-title" style="margin-top: 12px;">Acceso administrador</h2>

        <form method="POST" action="/login" class="stack">
            @csrf
            <div class="field">
                <label for="token">Token de administrador</label>
                <input type="password" name="token" id="token" class="input" required>
                @error('token')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn--navy btn--block">Iniciar sesión</button>
        </form>
    </div>
</div>
@endsection
