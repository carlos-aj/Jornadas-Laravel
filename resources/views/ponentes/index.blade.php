<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar Ponentes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
@include('layouts.navigation')

<x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="container mx-auto py-12 mt-16">
        <h1 class="text-4xl font-bold mb-8 text-center text-blue-700">Ponentes</h1>
        @if(Auth::check() && Auth::user()->role === 'admin')
            <form id="ponente-form" class="mb-12 p-8 bg-white rounded-lg shadow-lg" action="{{ route('ponentes.store') }}" method="POST" onsubmit="event.preventDefault(); crearPonente();">
                @csrf
                <input type="hidden" id="ponente-id">
                <div class="mb-6">
                    <label for="nombre" class="block text-lg font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-6">
                    <label for="fotografia" class="block text-lg font-medium text-gray-700">Fotografía (URL)</label>
                    <input type="text" id="fotografia" name="fotografia" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-6">
                    <label for="areas_experiencia" class="block text-lg font-medium text-gray-700">Áreas de Experiencia</label>
                    <input type="text" id="areas_experiencia" name="areas_experiencia" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-6">
                    <label for="redes_sociales" class="block text-lg font-medium text-gray-700">Redes Sociales</label>
                    <input type="text" id="redes_sociales" name="redes_sociales" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700">Guardar</button>
            </form>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="ponentes-container">
            @foreach($ponentes as $ponente)
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300" id="ponente-{{ $ponente->id }}">
                    <img src="{{ $ponente->fotografia }}" alt="{{ $ponente->nombre }}" class="w-full h-48 object-cover mb-4 rounded">
                    <h2 class="text-2xl font-bold mb-4 text-blue-700">{{ $ponente->nombre }}</h2>
                    <p class="text-gray-700 mb-4">{{ $ponente->areas_experiencia }}</p>
                    <p class="text-gray-700 mb-4">{{ $ponente->redes_sociales }}</p>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <form action="{{ route('ponentes.destroy', $ponente->id) }}" method="POST" onsubmit="event.preventDefault(); deletePonente({{ $ponente->id }});">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mt-6 bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700">Eliminar</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    <script>
        function crearPonente() {
            const form = document.getElementById('ponente-form');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    const ponentesContainer = document.getElementById('ponentes-container');
                    const ponenteDiv = document.createElement('div');
                    ponenteDiv.classList.add('bg-white', 'p-8', 'rounded-lg', 'shadow-lg', 'hover:shadow-xl', 'transition-shadow', 'duration-300');
                    ponenteDiv.id = `ponente-${data.id}`;
                    ponenteDiv.innerHTML = `
                        <img src="${data.fotografia}" alt="${data.nombre}" class="w-full h-48 object-cover mb-4 rounded">
                        <h2 class="text-2xl font-bold mb-4 text-blue-700">${data.nombre}</h2>
                        <p class="text-gray-700 mb-4">${data.areas_experiencia}</p>
                        <p class="text-gray-700 mb-4">${data.redes_sociales}</p>
                        <form action="/ponentes/${data.id}" method="POST" onsubmit="event.preventDefault(); deletePonente(${data.id});">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mt-6 bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700">Eliminar</button>
                        </form>
                    `;
                    ponentesContainer.appendChild(ponenteDiv);
                    form.reset();
                } else {
                    console.error('Error al crear el ponente:', data);
                }
            })
            .catch(error => console.error('Error al crear el ponente:', error));
        }

        function deletePonente(id) {
            fetch(`/ponentes/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById(`ponente-${id}`).remove();
                } else {
                    console.error('Error al eliminar el ponente');
                }
            })
            .catch(error => console.error('Error al eliminar el ponente:', error));
        }
    </script>
</body>
</html>
