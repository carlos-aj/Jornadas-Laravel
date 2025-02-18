<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Proyecto Laravel Jornadas')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    @include('layouts.navigation')

    <header class="bg-white shadow">
        <div class="container mx-auto py-6 px-4">
            <h1 class="font-semibold text-2xl text-gray-800 leading-tight">
                @yield('header')
            </h1>
        </div>
    </header>

    <main class="container mx-auto py-12 mt-16">
        @yield('content')
    </main>

    <script>
        @yield('scripts')
    </script>
</body>
</html>
