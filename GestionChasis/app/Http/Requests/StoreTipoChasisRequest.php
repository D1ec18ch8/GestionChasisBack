<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTipoChasisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('tipo_chasis', 'nombre')],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un tipo de chasis con ese nombre.',
        ];
    }
}
