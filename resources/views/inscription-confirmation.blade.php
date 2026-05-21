<div>
    <h1>Inscripción recibida</h1>
    <p>Se registraron {{ count($participants) }} participante(s):</p>
    
    @foreach ($participants as $index => $participant)
        <fieldset style="margin-bottom: 20px; padding: 15px; border: 1px solid #ccc;">
            <legend>Persona {{ $index + 1 }}</legend>
            <ul>
                <li><strong>Nombre:</strong> {{ $participant['name'] }}</li>
                <li><strong>Apellido:</strong> {{ $participant['lastname'] }}</li>
                <li><strong>DNI:</strong> {{ $participant['dni'] }}</li>
                <li><strong>Email:</strong> {{ $participant['email'] }}</li>
                <li><strong>Teléfono:</strong> {{ $participant['phone'] }}</li>
            </ul>
        </fieldset>
    @endforeach
</div>
