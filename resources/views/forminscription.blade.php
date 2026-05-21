<div>
    <h1>Formulario de Inscripción</h1>
    <form action="/inscription" method="POST">
        @csrf

        @for ($i = 0; $i < $quantity; $i++)
            <fieldset style="margin-bottom: 30px; padding: 15px; border: 1px solid #ccc;">
                <legend>Persona {{ $i + 1 }}</legend>

                <label for="name_{{ $i }}">Nombre:</label>
                <input type="text" id="name_{{ $i }}" name="participants[{{ $i }}][name]" required><br><br>

                <label for="lastname_{{ $i }}">Apellido:</label>
                <input type="text" id="lastname_{{ $i }}" name="participants[{{ $i }}][lastname]" required><br><br>

                <label for="dni_{{ $i }}">DNI:</label>
                <input type="text" id="dni_{{ $i }}" name="participants[{{ $i }}][dni]" required><br><br>

                <label for="email_{{ $i }}">Email:</label>
                <input type="email" id="email_{{ $i }}" name="participants[{{ $i }}][email]" required><br><br>

                <label for="phone_{{ $i }}">Telefono:</label>
                <input type="text" id="phone_{{ $i }}" name="participants[{{ $i }}][phone]" required><br><br>
                <div>
                    <input type="radio" id="presencial_{{ $i }}" name="participants[{{ $i }}][mode]" value="presencial" required>
                    <label for="presencial_{{ $i }}">Presencial</label>
                    <input type="radio" id="virtual_{{ $i }}" name="participants[{{ $i }}][mode]" value="virtual" required>
                    <label for="virtual_{{ $i }}">Virtual</label>
                </div>
            </fieldset>
        @endfor

        <button type="submit">Inscribirse</button>
    </form>
</div> 