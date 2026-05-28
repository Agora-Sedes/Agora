<div>
    <h2>Admin Panel</h2>
    <form action="{{Route('logout')}}" method="post">
        @csrf
        <button type="submit">Cerrar Sesión</button>
    </form>
    <a href="{{Route('qrscan')}}">Escanear QR</a>
    <a href="{{Route('attendants')}}">Inscritos</a>
    <a href="{{Route('addattendant')}}">Agregar Inscripto de ultimo momento</a>
</div>
