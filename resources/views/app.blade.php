<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ágora') – Sedes Sapientiae</title>
    <meta name="description" content="@yield('meta_description', 'Jornadas académicas de la Facultad Sedes Sapientiae')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-50 text-neutral-900 min-h-screen flex flex-col antialiased">

    {{-HEADER-}}
    <header class="bg-white border-b border-neutral-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between gap-4">

            {{-Logo-}}
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Inicio">
                {{-Ícono-}}
                <div class="w-9 h-9 rounded-lg bg-black flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 3L3 8.5V10H21V8.5L12 3Z" fill="currentColor"/>
                        <rect x="4" y="10" width="2" height="8" fill="currentColor"/>
                        <rect x="11" y="10" width="2" height="8" fill="currentColor"/>
                        <rect x="18" y="10" width="2" height="8" fill="currentColor"/>
                        <rect x="3" y="18" width="18" height="2" fill="currentColor"/>
                    </svg>
                </div>
                {{-Texto-}}
                <div class="leading-tight">
                    <span class="block text-base font-semibold text-neutral-900 tracking-tight">Ágora</span>
                    <span class="block text-[11px] text-neutral-500 font-medium tracking-wide uppercase">Sedes Sapientiae</span>
                </div>
            </a>

            {{-Acción derecha (Intranet)-}}
            @yield('header_action')
        </div>
    </header>

    {{-CONTENIDO-}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-FOOTER-}}
    <footer class="border-t border-neutral-200 bg-white mt-auto">
        <div class="max-w-5xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-neutral-500">
            <p>© {{ date('Y') }} Sedes Sapientiae · Todos los derechos reservados</p>
            <p class="text-xs">Ágora – Sistema de jornadas académicas</p>
        </div>
    </footer>

    @stack('scripts')

</body>
</html>
