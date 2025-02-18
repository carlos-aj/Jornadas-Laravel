<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function mostrarUsuarios(){
        $usuarios = Usuario::all();
        if ($usuarios->isEmpty()) {
            $data = [
                'message' => 'No se encontraron ponentes',
                'status' => 200
            ];
            return response()->json($data, 200);
        }
        $data = [
            'ponentes' => $usuarios,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function usuarioEstudiante(Request $request, $id){

        $usuario = User::find($id);

        if($usuario == null){
            return response()->json('Usuario no encontrado', 404);
        }

        $usuario->es_estudiante = true;
        $usuario->save();

        return response()->json([
            'mensaje' => 'Usuario actualizado correctamente',
            'usuario' => $usuario,
        ], 200);
    }
}
