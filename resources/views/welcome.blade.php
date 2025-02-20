<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Jornadas</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
        @endif
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold mb-6">Eventos</h1>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($eventos as $evento)
                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                <h2 class="text-xl font-bold mb-2">{{ $evento->titulo }}</h2>
                                <p class="text-gray-700 mb-4">{{ $evento->tipo }}</p>
                                <p class="text-gray-700 mb-4">{{ $evento->fecha }} - {{ $evento->hora }}</p>
                                <p class="text-gray-700 mb-4">Cupo máximo: {{ $evento->cupo_maximo }}</p>
                                @if ($evento->ponente)
                                    <p class="text-gray-700">Ponente: {{ $evento->ponente->nombre }}</p>
                                @else
                                    <p class="text-gray-700">Ponente: No asignado</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <h1 class="text-3xl font-bold mt-10 mb-6">Ponentes</h1>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($ponentes as $ponente)
                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                <h2 class="text-xl font-bold mb-2">{{ $ponente->nombre }}</h2>
                                <p class="text-gray-700 mb-4">{{ $ponente->areas_experiencia }}</p>
                                <p class="text-gray-700">Redes sociales: {{ $ponente->redes_sociales }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
