<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChasisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_chasis_id' => ['required', 'integer', 'exists:tipo_chasis,id'],
            'ubicacion_id' => ['nullable', 'integer', 'exists:ubicaciones,id'],
            'nombre' => ['required', 'string', 'max:255', Rule::unique('chasis', 'nombre')],
            'categoria' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'integer', Rule::unique('chasis', 'numero')],
            'estado' => ['prohibited'],
            'estado_id' => ['prohibited'],
            'averia_patas' => ['sometimes', 'boolean'],
            'averia_luces' => ['sometimes', 'boolean'],
            'averia_manoplas' => ['sometimes', 'boolean'],
            'averia_mangueras' => ['sometimes', 'boolean'],
            'averia_llantas' => ['sometimes', 'boolean'],
            'placa' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un chasis con ese nombre.',
            'numero.unique' => 'Ya existe un chasis con ese numero.',
        ];
    }
}
