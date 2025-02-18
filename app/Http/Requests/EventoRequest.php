<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Eventos;

class EventoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all users to create an event for now
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
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'cupo_maximo' => 'required|integer|min:1',
            'ponente_id' => 'required|exists:ponente,id',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es requerido',
            'tipo.required' => 'El tipo es requerido',
            'fecha.required' => 'La fecha es requerida',
            'hora.required' => 'La hora es requerida',
            'cupo_maximo.required' => 'El cupo máximo es requerido',
            'ponente_id.required' => 'El ponente es requerido',
            'ponente_id.exists' => 'El ponente no existe',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $tipo = $this->input('tipo');
            $fecha = $this->input('fecha');
            $hora = $this->input('hora');

            $eventoExistente = Eventos::where('tipo', $tipo)
                ->where('fecha', $fecha)
                ->where('hora', $hora)
                ->first();

            if ($eventoExistente) {
                $validator->errors()->add('tipo', 'Ya existe un evento de este tipo en la misma fecha y hora.');
            }
        });
    }
}
