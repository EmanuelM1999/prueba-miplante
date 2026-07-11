<?php

namespace App\Http\Requests\Credito;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ResumenRequest extends FormRequest
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
            'cuotas' => 'required|array|min:1',
            'cuotas.*.valor' => 'required|numeric|min:0',
            'cuotas.*.fecha' => 'required|date',
        ];
    }

    //Devuelve los mensajes personalizados para cada tipo de validación.
    public function messages(): array
    {
        return [
            'cuotas.required' => 'Debe enviar la lista de cuotas.',
            'cuotas.array' => 'El campo cuotas debe ser un arreglo.',
            'cuotas.min' => 'Debe enviar al menos una cuota.',

            'cuotas.*.valor.required' => 'El valor de la cuota es obligatorio.',
            'cuotas.*.valor.numeric' => 'El valor de la cuota debe ser numérico.',
            'cuotas.*.valor.min' => 'El valor de la cuota no puede ser negativo.',

            'cuotas.*.fecha.required' => 'La fecha de la cuota es obligatoria.',
            'cuotas.*.fecha.date' => 'La fecha de la cuota debe tener un formato válido.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'code' => 422,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
