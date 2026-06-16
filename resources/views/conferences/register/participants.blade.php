@extends("layouts.app")

@section('title', "Completa el formulario de inscripción")

@section("css")
    <link rel="stylesheet" href="{{ url('css/forminscription.css') }}">
@endsection

@section('content')
<div class="content container section">
    <div class="form-container panel">
        <h1 class="page-title">Formulario de Inscripción</h1>
        <form action="{{ route('conferences.register.step-3', [ 'id' => $conference_id ]) }}" method="POST">
            @csrf

            <input type="hidden" name="payment_method" value="{{ $method }}">
            @for ($i = 0; $i < $amount; $i++)
                <fieldset>
                <legend>Persona {{ $i + 1 }}</legend>

                <div class="field">
                    <label for="name_{{ $i }}">Nombre</label>
                    <input class="input" type="text" id="name_{{ $i }}" name="participants[{{ $i }}][name]" required>
                </div>
                <div class="field">
                    <label for="lastname_{{ $i }}">Apellido</label>
                    <input class="input" type="text" id="lastname_{{ $i }}" name="participants[{{ $i }}][lastname]" required>
                </div>
                <div class="field">
                    <label for="dni_{{ $i }}">DNI</label>
                    <input class="input" type="text" id="dni_{{ $i }}" name="participants[{{ $i }}][dni]" required>
                </div>
                <div class="field">
                    <label for="email_{{ $i }}">Email</label>
                    <input class="input" type="email" id="email_{{ $i }}" name="participants[{{ $i }}][email]" required>
                </div>
                <div class="field">
                    <label for="phone_{{ $i }}">Teléfono</label>
                    <input class="input" type="text" id="phone_{{ $i }}" name="participants[{{ $i }}][phone]" required>
                </div>
                <div class="field">
                    <label>Modalidad</label>
                    <div class="form__radio-group">
                        <label class="form__radio">
                            <input type="radio" id="irl_{{ $i }}" name="participants[{{ $i }}][mode]" value="irl" required>
                            Presencial
                        </label>
                        <label class="form__radio">
                            <input type="radio" id="online_{{ $i }}" name="participants[{{ $i }}][mode]" value="online" required>
                            Virtual
                        </label>
                    </div>
                </div>
                </fieldset>
            @endfor

            <button class="btn btn--primary btn--block" type="submit">Inscribirse</button>
        </form>
    </div>
</div>
@endsection
