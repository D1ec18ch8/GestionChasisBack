<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:estados,nombre'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:estados,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un estado con ese nombre.',
            'slug.unique' => 'Ya existe un estado con ese slug.',
        ];
    }
}
