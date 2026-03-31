<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'string', 'email', 'max:200', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                //regra de complexidade minima de senha
                Password::min(8)->letters()->numbers()
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Este E-mail já esta em uso.',
            'password.confirm' => 'As senhas não coincidem.'
        ];
    }
}
