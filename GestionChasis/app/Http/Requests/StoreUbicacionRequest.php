<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUbicacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['nullable', 'string', 'max:255', Rule::unique('ubicaciones', 'codigo')],
            'nombre' => ['required', 'string', 'max:255', Rule::unique('ubicaciones', 'nombre')],
            'razon_social' => ['required', 'string', 'max:255'],
            'aduana' => ['nullable', 'string', 'max:10'],
            'direccion' => ['nullable', 'string'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'Ya existe una ubicacion con ese codigo.',
            'nombre.unique' => 'Ya existe una ubicacion con ese nombre.',
        ];
    }
}
