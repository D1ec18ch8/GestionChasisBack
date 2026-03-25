<?php

namespace App\Http\Requests;

use App\Models\Estado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Estado|null $estado */
        $estado = $this->route('estado');

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('estados', 'nombre')->ignore($estado?->id)],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash', Rule::unique('estados', 'slug')->ignore($estado?->id)],
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
