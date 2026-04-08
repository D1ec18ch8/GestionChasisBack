<?php

namespace App\Http\Requests;

use App\Models\Chasis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChasisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Chasis|null $chasis */
        $chasis = $this->route('chasis');

        return [
            'tipo_chasis_id' => ['sometimes', 'required', 'integer', 'exists:tipo_chasis,id'],
            'ubicacion_id' => ['nullable', 'integer', 'exists:ubicaciones,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('chasis', 'nombre')->ignore($chasis?->id)],
            'numero' => ['nullable', 'integer', Rule::unique('chasis', 'numero')->ignore($chasis?->id)],
            'estado' => ['prohibited'],
            'estado_id' => ['prohibited'],
            'averia_patas' => ['sometimes', 'boolean'],
            'averia_luces' => ['sometimes', 'boolean'],
            'averia_manoplas' => ['sometimes', 'boolean'],
            'averia_mangueras' => ['sometimes', 'boolean'],
            'averia_llantas' => ['sometimes', 'boolean'],
            'placa' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('chasis', 'placa')->ignore($chasis?->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un chasis con ese nombre.',
            'placa.required' => 'La placa es obligatoria.',
            'placa.unique' => 'Ya existe un chasis con esa placa.',
            'numero.unique' => 'Ya existe un chasis con ese numero.',
        ];
    }
}
