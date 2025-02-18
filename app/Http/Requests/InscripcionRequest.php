<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => 'required|exists:usuarios,id',
            'evento_id' => 'required|exists:eventos,id',
            'tipo_inscripcion' => 'required|string|in:presencial,virtual',
            'es_estudiante' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_id.required' => 'El usuario es requerido',
            'usuario_id.exists' => 'El usuario no existe',
            'evento_id.required' => 'El evento es requerido',
            'evento_id.exists' => 'El evento no existe',
            'tipo_inscripcion.required' => 'El tipo de inscripción es requerido',
            'tipo_inscripcion.in' => 'El tipo de inscripción no es válido',
            'es_estudiante.required' => 'El estado de estudiante es requerido',
            'es_estudiante.boolean' => 'El estado de estudiante debe ser verdadero o falso',
        ];
    }
}
