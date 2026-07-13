@extends('layouts.app')

@section('title', 'Editar inscripto')

@section('header_action')
    <a href="{{ route('intranet.conferences.attendants.list', [ 'id' => $conference->id ]) }}" class="header-action">← Lista</a>
@endsection

@section('content')
<div class="center-page">
    <div class="panel">
        <h2 class="page-title">Editar inscripto</h2>

        <form action="{{ route('intranet.conferences.attendants.update', [ 'id' => $conference->id, 'attendantId' => $attendant->id ]) }}" method="post" class="stack">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" class="input" value="{{ explode(' ', $attendant->full_name, 2)[0] }}" required>
            </div>
            <div class="field">
                <label for="lastname">Apellido</label>
                <input type="text" id="lastname" name="lastname" class="input" value="{{ explode(' ', $attendant->full_name, 2)[1] ?? '' }}" required>
            </div>
            <div class="field">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" class="input" value="{{ $attendant->government_id }}" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="input" value="{{ $attendant->email }}" required>
            </div>
            <div class="field">
                <label for="phone">Teléfono</label>
                <input type="text" id="phone" name="phone" class="input" value="{{ $attendant->phone_number }}" required>
            </div>
            <label class="form__radio" style="display: inline-flex; gap: 8px; align-items: center;">
                <input type="checkbox" name="is_draft" value="1" {{ $attendant->is_draft ? 'checked' : '' }}>
                Marcar como impago
            </label>
            <button type="submit" class="btn btn--primary btn--block">Guardar cambios</button>
        </form>
    </div>
</div>
@endsection
