<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUbicacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ubicacionId = $this->route('ubicacion')?->id;

        return [
            'codigo' => ['nullable', 'string', 'max:255', Rule::unique('ubicaciones', 'codigo')->ignore($ubicacionId)],
            'nombre' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('ubicaciones', 'nombre')->ignore($ubicacionId)],
            'razon_social' => ['sometimes', 'required', 'string', 'max:255'],
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
