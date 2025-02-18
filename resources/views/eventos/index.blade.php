<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar Eventos</title>
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
        <h1 class="text-4xl font-bold mb-8 text-center text-blue-700">Eventos</h1>
        @if(Auth::check() && Auth::user()->role === 'admin')
            <form id="evento-form" class="mb-12 p-8 bg-white rounded-lg shadow-lg" action="{{ route('eventos.store') }}" method="POST" onsubmit="event.preventDefault(); crearEvento();">
                @csrf
                <input type="hidden" id="evento-id">
                <div class="mb-6">
                    <label for="titulo" class="block text-lg font-medium text-gray-700">Título</label>
                    <input type="text" id="titulo" name="titulo" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-6">
                    <label for="tipo" class="block text-lg font-medium text-gray-700">Tipo</label>
                    <select id="tipo" name="tipo" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Conferencia">Conferencia</option>
                        <option value="Taller">Taller</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="fecha" class="block text-lg font-medium text-gray-700">Fecha</label>
                    <input type="date" id="fecha" name="fecha" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-6">
                    <label for="hora" class="block text-lg font-medium text-gray-700">Hora</label>
                    <input type="time" id="hora" name="hora" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-6">
                    <label for="cupo_maximo" class="block text-lg font-medium text-gray-700">Cupo Máximo</label>
                    <input type="number" id="cupo_maximo" name="cupo_maximo" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-6">
                    <label for="ponente_id" class="block text-lg font-medium text-gray-700">Ponente</label>
                    <select id="ponente_id" name="ponente_id" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($ponentes as $ponente)
                            <option value="{{ $ponente->id }}">{{ $ponente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700">Guardar</button>
            </form>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="eventos-container">
            @foreach($eventos as $evento)
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300" id="evento-{{ $evento->id }}">
                    <h2 class="text-2xl font-bold mb-4 text-blue-700">{{ $evento->titulo }}</h2>
                    <p class="text-gray-700 mb-4">{{ $evento->tipo }}</p>
                    <p class="text-gray-700 mb-4">{{ $evento->fecha }} - {{ $evento->hora }}</p>
                    <p class="text-gray-700 mb-4">Cupo máximo: {{ $evento->cupo_maximo }}</p>
                    @if ($evento->ponente)
                        <p class="text-gray-700">Ponente: {{ $evento->ponente->nombre }}</p>
                    @else
                        <p class="text-gray-700">Ponente: No asignado</p>
                    @endif
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <form action="{{ route('eventos.destroy', $evento->id) }}" method="POST" onsubmit="event.preventDefault(); deleteEvento({{ $evento->id }});">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mt-6 bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700">Eliminar</button>
                        </form>
                    @else
                        <form action="{{ route('eventos.inscribirse', $evento->id) }}" method="POST" onsubmit="event.preventDefault(); inscribirseEvento({{ $evento->id }});">
                            @csrf
                            <div class="mb-6">
                                <label for="tipo" class="block text-lg font-medium text-gray-700">Tipo de inscripción</label>
                                <select id="tipo" name="tipo" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="Presencial">Presencial</option>
                                    <option value="Virtual">Virtual</option>
                                </select>
                            </div>
                            <button type="submit" class="mt-6 bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700">Inscribirse</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    <script>
        function crearEvento() {
            const form = document.getElementById('evento-form');
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
                    const eventosContainer = document.getElementById('eventos-container');
                    const eventoDiv = document.createElement('div');
                    eventoDiv.classList.add('bg-white', 'p-8', 'rounded-lg', 'shadow-lg', 'hover:shadow-xl', 'transition-shadow', 'duration-300');
                    eventoDiv.id = `evento-${data.id}`;
                    eventoDiv.innerHTML = `
                        <h2 class="text-2xl font-bold mb-4 text-blue-700">${data.titulo}</h2>
                        <p class="text-gray-700 mb-4">${data.tipo}</p>
                        <p class="text-gray-700 mb-4">${data.fecha} - ${data.hora}</p>
                        <p class="text-gray-700 mb-4">Cupo máximo: ${data.cupo_maximo}</p>
                        <p class="text-gray-700">Ponente: ${data.ponente ? data.ponente.nombre : 'No asignado'}</p>
                        <form action="/eventos/${data.id}" method="POST" onsubmit="event.preventDefault(); deleteEvento(${data.id});">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mt-6 bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700">Eliminar</button>
                        </form>
                    `;
                    eventosContainer.appendChild(eventoDiv);
                    form.reset();
                } else {
                    console.error('Error al crear el evento:', data);
                }
            })
            .catch(error => console.error('Error al crear el evento:', error));
        }

        function deleteEvento(id) {
            fetch(`/eventos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById(`evento-${id}`).remove();
                } else {
                    console.error('Error al eliminar el evento');
                }
            })
            .catch(error => console.error('Error al eliminar el evento:', error));
        }

        function inscribirseEvento(id) {
            const form = document.querySelector(`#evento-${id} form`);
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
                if (data === 'Inscripción realizada con éxito') {
                    alert('Inscripción realizada con éxito');
                } else {
                    console.error('Error al inscribirse:', data);
                }
            })
            .catch(error => console.error('Error al inscribirse:', error));
        }
    </script>
</body>
</html>
