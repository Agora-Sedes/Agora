<div>
    <h2>Agregar inscripto de ultimo momento</h2>
    <form action="" method="post">
        @csrf
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" required><br><br>
        <label for="lastname">Apellido:</label>
        <input type="text" id="lastname" name="lastname" required><br><br>
        <label for="dni">DNI:</label>
        <input type="text" id="dni" name="dni" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="phone">Telefono:</label>
        <input type="text" id="phone" name="phone" required><br><br>
    </form>
</div>
