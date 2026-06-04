<div>
    <div>
        <h2>Seleccionar Conferencia</h2>
        <form action="" method="post">
            @csrf
            <label for="conference">Conferencia:</label>
            <select id="conference" name="conference" required>
                @foreach($conferences as $conference)
                    <option value="{{ $conference->id }}">Tu mama es mi novia</option>
                @endforeach
            </select><br><br>
            <button type="submit">Seleccionar Conferencia</button>
        </form>
    </div><!-- Breathing in, I calm body and mind. Breathing out, I smile. - Thich Nhat Hanh -->
</div>
