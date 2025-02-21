<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Http\Requests\InscripcionRequest;
use Illuminate\Http\Request;
use App\Models\Evento;

class InscripcionController extends Controller
{
    public function crearInscripcion(InscripcionRequest $request)
    {
        $validated = $request->validated();

        // Check if the user is already registered for the event
        $existingInscripcion = Inscripcion::where('usuario_id', $validated['usuario_id'])
                                          ->where('evento_id', $validated['evento_id'])
                                          ->first();

        if ($existingInscripcion) {
            return response()->json(['error' => 'Ya estás inscrito en este evento.'], 400);
        }

        // Find the event and check capacity
        $evento = Evento::find($validated['evento_id']);
        if ($evento->cupo_maximo <= 0) {
            return response()->json(['error' => 'No hay cupo disponible para este evento.'], 400);
        }

        $precio = $this->calcularPrecio($validated['tipo_inscripcion'], $validated['es_estudiante']);

        $inscripcion = new Inscripcion();
        $inscripcion->usuario_id = $validated['usuario_id'];
        $inscripcion->evento_id = $validated['evento_id'];
        $inscripcion->tipo_inscripcion = $validated['tipo_inscripcion'];
        $inscripcion->es_estudiante = $validated['es_estudiante'];
        $inscripcion->precio = $precio;

        $inscripcion->save();

        $evento->cupo_maximo -= 1;
        $evento->save();

        return response()->json(['inscripcion' => $inscripcion, 'cupo_maximo' => $evento->cupo_maximo], 201);
    }

    private function calcularPrecio($tipoInscripcion, $esEstudiante)
    {
        if ($esEstudiante) {
            return 0;
        }
        switch ($tipoInscripcion) {
            case 'virtual':
                return 5;
            case 'presencial':
                return 10;
            default:
                return 0;
        }
    }

    public function eliminarInscripcion($id)
    {
        $inscripcion = Inscripcion::find($id);
        if ($inscripcion == null) {
            return response()->json('Inscripción no encontrada', 404);
        }
        $inscripcion->delete();

        return response()->json('Inscripción eliminada', 204);
    }

    public function mostrarInscripciones(){
        $inscripciones = Inscripcion::all();

        if($inscripciones == null){
            return response()->json('No hay inscripciones', 404);
        }

        return response()->json($inscripciones);
    }
}
