<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ágora') – Sedes Sapientiae</title>
    <meta name="description" content="@yield('meta_description', 'Jornadas académicas de la Facultad Sedes Sapientiae')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    {{-HEADER-}}
    <header>
        <div>

            {{-Logo-}}
            <a href="{{ route('home') }}" aria-label="Inicio">
                {{-Ícono-}}
                <div>
                </div>
                {{-Texto-}}
                <div class="leading-tight">
                    <span>Ágora</span>
                    <span>Sedes Sapientiae</span>
                </div>
            </a>

            {{-Acción derecha (Intranet)-}}
            @yield('header_action')
        </div>
    </header>

    {{-CONTENIDO-}}
    <main>
        @yield('content')
    </main>

    {{-FOOTER-}}
    <footer>
        <div>
            <p>© {{ date('Y') }} Sedes Sapientiae · Todos los derechos reservados</p>
            <p class="text-xs">Ágora – Sistema de jornadas académicas</p>
        </div>
    </footer>

    @stack('scripts')

</body>
</html>
