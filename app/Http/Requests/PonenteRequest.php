<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PonenteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string'],
            'fotografia' => ['required', 'string'],
            'areas_experiencia' => ['required', 'string'],
            'redes_sociales' => ['required', 'string']
        ];
    }

    public function message(): array
    {
        return [
            'nombre.required' => 'El nombre es requerido',
            'fotografia.required' => 'La fotografia es requerida',
            'areas_experiencia.required' => 'Las áreas de experiencia son requeridas',
            'redes_sociales.required' => 'Las redes sociales son requeridas'
        ];
    }

}
