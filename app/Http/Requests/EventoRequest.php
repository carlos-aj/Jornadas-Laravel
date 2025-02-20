<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Eventos;
use Carbon\Carbon;

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

            $eventoHora = Carbon::createFromFormat('Y-m-d H:i', "$fecha $hora");

            $eventoExistente = Eventos::where('tipo', $tipo)
                ->where('fecha', $fecha)
                ->get()
                ->filter(function ($evento) use ($eventoHora) {
                    $eventoInicio = Carbon::createFromFormat('Y-m-d H:i', "$evento->fecha $evento->hora");
                    return $eventoHora->diffInMinutes($eventoInicio) < 55;
                })
                ->first();

            if ($eventoExistente) {
                $validator->errors()->add('hora', 'Debe haber al menos 55 minutos entre eventos del mismo tipo.');
            }
        });
    }
}
