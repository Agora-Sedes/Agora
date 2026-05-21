<div>
    <form action="{{Route('logout')}}" method="post">
        @csrf
        <button type="submit">Cerrar Sesión</button>
    </form>
</div>
