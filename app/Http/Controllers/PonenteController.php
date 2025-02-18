<?php

namespace App\Http\Controllers;

use App\Models\Ponente;
use App\Http\Requests\PonenteRequest;
use Illuminate\Support\Facades\Auth;

class PonenteController extends Controller
{
    /**
     * Crear un nuevo ponente.
     */
    public function crearPonente(PonenteRequest $request)
    {
        $validated = $request->validated();

        $ponente = new Ponente();
        $ponente->nombre = $validated['nombre'];
        $ponente->fotografia = $validated['fotografia'];
        $ponente->areas_experiencia = $validated['areas_experiencia'];
        $ponente->redes_sociales = $validated['redes_sociales'];

        $ponente->save();
        
        return response()->json($ponente, 201);
    }

    /**
     * Mostrar todos los ponentes.
     */
    public function mostrarPonentes()
    {
        $ponentes = Ponente::all();
        if ($ponentes->isEmpty()) {
            $data = [
                'message' => 'No se encontraron ponentes',
                'status' => 200
            ];
            return response()->json($data, 200);
        }
        $data = [
            'ponentes' => $ponentes,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Mostrar la vista de ponentes.
     */
    public function index()
    {
        $ponentes = Ponente::all();
        return view('ponentes.index', compact('ponentes'));
    }

    /**
     * Eliminar un ponente.
     */
    public function eliminarPonente($id)
    {
        $ponente = Ponente::find($id);
        if($ponente == null){
            return response()->json('Ponente no encontrado', 404);
        }
        $ponente->delete();
        
        return response()->json('Ponente eliminado', 204);
    }
}
