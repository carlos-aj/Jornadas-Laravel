<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Inscripciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
@include('layouts.navigation')

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Mis Inscripciones') }}
    </h2>
</x-slot>
<div class="container mx-auto py-8 mt-16">
    <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">{{ Auth::user()->role === 'admin' ? 'Todas las Inscripciones' : 'Mis Inscripciones' }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="inscripciones-container">
        @foreach($inscripciones as $inscripcion)
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300" id="inscripcion-{{ $inscripcion->id }}">
                <h2 class="text-xl font-bold mb-2 text-blue-600">{{ $inscripcion->evento->titulo }}</h2>
                <p class="text-gray-700 mb-4">{{ $inscripcion->evento->tipo }}</p>
                <p class="text-gray-700 mb-4">{{ $inscripcion->evento->fecha }} - {{ $inscripcion->evento->hora }}</p>
                <p class="text-gray-700 mb-4">Tipo de inscripción: {{ $inscripcion->tipo_inscripcion }}</p>
                @if(Auth::user()->role === 'admin' || Auth::id() === $inscripcion->user_id)
                    <form action="{{ route('inscripciones.destroy', $inscripcion->id) }}" method="POST" onsubmit="event.preventDefault(); deleteInscripcion({{ $inscripcion->id }});">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Eliminar</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>
<script>
    function deleteInscripcion(id) {
        fetch(`/inscripciones/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                document.getElementById(`inscripcion-${id}`).remove();
            } else {
                console.error('Error al eliminar la inscripción');
            }
        })
        .catch(error => console.error('Error al eliminar la inscripción:', error));
    }
</script>
</body>
</html>
