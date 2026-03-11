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
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'integer', Rule::unique('chasis', 'numero')],
            'estado' => ['required', 'string', 'max:255'],
            'placa' => ['nullable', 'string', 'max:255'],
        ];
    }
}
