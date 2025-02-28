<?php

namespace App\Http\Controllers;

use App\Models\Eventos;
use App\Models\Ponente;
use App\Models\Inscripcion;
use App\Http\Requests\EventoRequest;
use App\Notifications\InscripcionNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class EventoController extends Controller
{
    /**
     * Mostrar todos los eventos.
     */
    public function mostrarEventos()
    {
        $eventos = Eventos::with('ponente')->get();
        if ($eventos->isEmpty()) {
            $data = [
                'message' => 'No se encontraron eventos',
                'status' => 200
            ];
            return response()->json($data, 200);
        }
        $data = [
            'eventos' => $eventos,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Crear un nuevo evento.
     */
    public function crearEvento(EventoRequest $request)
    {
        $validated = $request->validated();
        
        try {
            $evento = new Eventos();
            $evento->titulo = $validated['titulo'];
            $evento->tipo = $validated['tipo'];
            $evento->fecha = $validated['fecha'];
            $evento->hora = $validated['hora'];
            $evento->cupo_maximo = $validated['cupo_maximo'];
            $evento->ponente_id = $validated['ponente_id'];

            $evento->save();
            
            return response()->json($evento, 201);
        } catch (\Exception $e) {
            Log::error('Error al crear el evento: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error al crear el evento',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Mostrar la vista de eventos.
     */
    public function index()
    {
        $eventos = Eventos::with('ponente')->get();
        $ponentes = Ponente::all();
        return view('eventos.index', compact('eventos', 'ponentes'));
    }

    /**
     * Eliminar un evento.
     */
    public function eliminarEvento($id)
    {
        $evento = Eventos::find($id);
        if($evento == null){
            return response()->json('Evento no encontrado', 404);
        }
        $evento->delete();
        
        return response()->json('Evento eliminado', 204);
    }

    /**
     * Inscribirse en un evento.
     */
    public function inscribirse(Request $request, $id)
    {
        if (Auth::check()) {
            $evento = Eventos::find($id);
            if ($evento == null) {
                return response()->json('Evento no encontrado', 404);
            }

            $inscripcionExistente = Inscripcion::where('user_id', Auth::id())
                ->where('evento_id', $id)
                ->first();

            if ($inscripcionExistente) {
                return response()->json('Ya estás inscrito en este evento', 409);
            }

            $tipoInscripcion = $request->input('tipo_inscripcion');
            if (!$tipoInscripcion) {
                return response()->json('Tipo de inscripción es requerido', 400);
            }

            // Redirigir a PayPal para el pago
            return redirect()->route('make.payment', ['id' => $id, 'tipo_inscripcion' => $tipoInscripcion]);
        } else {
            return response()->json(['error' => 'Debes estar autenticado para inscribirte'], 403);
        }
    }

    /**
     * Ver inscripciones del usuario.
     */
    public function verInscripciones()
    {
        if (Auth::user()->role === 'admin') {
            $inscripciones = Inscripcion::with('evento')->get();
        } else {
            $inscripciones = Inscripcion::with('evento')
                ->where('user_id', Auth::id())
                ->get();
        }

        return view('inscripciones.index', compact('inscripciones'));
    }

    /**
     * Eliminar una inscripción.
     */
    public function eliminarInscripcion($id)
    {
        $inscripcion = Inscripcion::find($id);
        if ($inscripcion == null || (Auth::user()->role !== 'admin' && $inscripcion->user_id != Auth::id())) {
            return response()->json('Inscripción no encontrada o no autorizada', 404);
        }
        $inscripcion->delete();

        return response()->json('Inscripción eliminada', 204);
    }
}