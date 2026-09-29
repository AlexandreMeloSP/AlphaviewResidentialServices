<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $this->user()?->id],
            'bio' => ['nullable', 'string', 'max:500'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'endereco' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'bio.max' => 'A biografia deve ter no máximo 500 caracteres.',
            'telefone.max' => 'O telefone deve ter no máximo 20 caracteres.',
            'endereco.max' => 'O endereço deve ter no máximo 255 caracteres.',
        ];
    }
}
