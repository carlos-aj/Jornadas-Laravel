<nav class="bg-blue-700 p-6 shadow-lg">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-white text-2xl font-bold">Inicio</a>
        <div class="flex space-x-6">
            <a href="{{ route('eventos') }}" class="text-white hover:text-gray-200 text-lg">Eventos</a>
            <a href="{{ route('ponentes') }}" class="text-white hover:text-gray-200 text-lg">Ponentes</a>
            @if(Auth::check())
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:text-gray-200 text-lg">Cerrar sesión</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-white hover:text-gray-200 text-lg">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="text-white hover:text-gray-200 text-lg">Registrarse</a>
            @endif
        </div>
    </div>
</nav>
