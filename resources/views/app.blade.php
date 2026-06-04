<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ágora') – Sedes Sapientiae</title>
    <meta name="description" content="@yield('meta_description', 'Jornadas académicas de la Facultad Sedes Sapientiae')">

</head>
<body>

    <header>
        <div>

            <a href="{{ route('home') }}" aria-label="Inicio">
                
                <div>
                </div>
                
                <div class="leading-tight">
                    <span>Ágora</span>
                    <span>Sedes Sapientiae</span>
                </div>
            </a>
            @yield('header_action')
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <div>
            <p>© {{ date('Y') }} Sedes Sapientiae · Todos los derechos reservados</p>
            <p class="text-xs">Ágora – Sistema de jornadas académicas</p>
        </div>
    </footer>

    @stack('scripts')

</body>
</html>
