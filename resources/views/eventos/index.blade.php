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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="container mx-auto py-8 mt-16">
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-600">Eventos</h1>
        @if(Auth::check() && Auth::user()->role === 'admin')
            <form id="evento-form" class="mb-8 p-6 bg-white rounded-lg shadow-md" action="{{ route('eventos.store') }}" method="POST" onsubmit="event.preventDefault(); crearEvento();">
                @csrf
                <input type="hidden" id="evento-id">
                <div class="mb-4">
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" id="titulo" name="titulo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select id="tipo" name="tipo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Conferencia">Conferencia</option>
                        <option value="Taller">Taller</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" id="fecha" name="fecha" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="hora" class="block text-sm font-medium text-gray-700">Hora</label>
                    <input type="time" id="hora" name="hora" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="cupo_maximo" class="block text-sm font-medium text-gray-700">Cupo Máximo</label>
                    <input type="number" id="cupo_maximo" name="cupo_maximo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="ponente_id" class="block text-sm font-medium text-gray-700">Ponente</label>
                    <select id="ponente_id" name="ponente_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($ponentes as $ponente)
                            <option value="{{ $ponente->id }}">{{ $ponente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Guardar</button>
            </form>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="eventos-container">
            @foreach($eventos as $evento)
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300" id="evento-{{ $evento->id }}">
                    <h2 class="text-xl font-bold mb-2 text-blue-600">{{ $evento->titulo }}</h2>
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
                            <button type="submit" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Eliminar</button>
                        </form>
                    @elseif(Auth::check())
                        <form action="{{ route('make.payment', ['id' => $evento->id, 'tipo_inscripcion' => 'Presencial', 'price' => '10.00']) }}" method="POST" id="payment-form-{{ $evento->id }}" onsubmit="event.preventDefault(); submitPaymentForm({{ $evento->id }});">
                            @csrf
                            <div class="mb-4">
                                <label for="tipo_inscripcion-{{ $evento->id }}" class="block text-sm font-medium text-gray-700">Tipo de inscripción</label>
                                <select id="tipo_inscripcion-{{ $evento->id }}" name="tipo_inscripcion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="updatePrice({{ $evento->id }})">
                                    <option value="Presencial" data-price="10.00">Presencial - 10 €</option>
                                    <option value="Virtual" data-price="5.00">Virtual - 5 €</option>
                                </select>
                            </div>
                            <input type="hidden" id="price-{{ $evento->id }}" name="price" value="10.00">
                            <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Inscribirse</button>
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
                    eventoDiv.classList.add('bg-white', 'p-6', 'rounded-lg', 'shadow-lg', 'hover:shadow-xl', 'transition-shadow', 'duration-300');
                    eventoDiv.id = `evento-${data.id}`;
                    eventoDiv.innerHTML = `
                        <h2 class="text-xl font-bold mb-2 text-blue-600">${data.titulo}</h2>
                        <p class="text-gray-700 mb-4">${data.tipo}</p>
                        <p class="text-gray-700 mb-4">${data.fecha} - ${data.hora}</p>
                        <p class="text-gray-700 mb-4">Cupo máximo: ${data.cupo_maximo}</p>
                        <p class="text-gray-700">Ponente: ${data.ponente ? data.ponente.nombre : 'No asignado'}</p>
                        <form action="/eventos/${data.id}" method="POST" onsubmit="event.preventDefault(); deleteEvento(${data.id});">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Eliminar</button>
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

        function updatePrice(eventoId) {
            const tipoInscripcionSelect = document.getElementById(`tipo_inscripcion-${eventoId}`);
            const selectedOption = tipoInscripcionSelect.options[tipoInscripcionSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            document.getElementById(`price-${eventoId}`).value = price;
        }

        function submitPaymentForm(eventoId) {
            const form = document.getElementById(`payment-form-${eventoId}`);
            const tipoInscripcion = document.getElementById(`tipo_inscripcion-${eventoId}`).value;
            const price = document.getElementById(`price-${eventoId}`).value;
            form.action = `{{ url('/make-payment') }}/${eventoId}/${tipoInscripcion}?price=${price}`;
            form.submit();
        }

        function refreshCupo(eventoId, newCupo) {
            const eventoDiv = document.getElementById(`evento-${eventoId}`);
            const cupoElement = eventoDiv.querySelector('.cupo-maximo');
            cupoElement.textContent = `Cupo máximo: ${newCupo}`;
        }
    </script>
</body>
</html>
