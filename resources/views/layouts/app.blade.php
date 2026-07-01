<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ágora') – Sedes Sapientiae</title>
    <meta name="description" content="@yield('meta_description', 'Jornadas académicas de la Facultad Sedes Sapientiae')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ url("css/app.css") }}">

    @yield('css')
</head>
<body>

    <header class="site-header">
        <div class="container site-header__inner">
            <a href="{{ route('conferences.list') }}" class="brand" aria-label="Inicio">
                <span class="brand__mark">Á</span>
                <span class="brand__text">
                    <span class="brand__name">Ágora</span>
                    <span class="brand__sub">Sedes Sapientiae</span>
                </span>
            </a>
            @yield('header_action')
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>Copyright {{ date('Y') }}, equipo de desarrollo de <a href="https://github.com/Agora-Sedes/Agora">Ágora</a></p>
            <p class="text-xs">Instituto de Profesorado Sedes Sapientiae</p>
        </div>
    </footer>

    @stack('scripts')

</body>
</html>
