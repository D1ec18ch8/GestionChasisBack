<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es requerido.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Ya existe un usuario con ese correo electrónico.',
            'password.required' => 'La contraseña es requerida.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
