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
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'integer', Rule::unique('chasis', 'numero')->ignore($chasis?->id)],
            'estado' => ['sometimes', 'required', 'string', 'max:255'],
            'placa' => ['nullable', 'string', 'max:255'],
        ];
    }
}
