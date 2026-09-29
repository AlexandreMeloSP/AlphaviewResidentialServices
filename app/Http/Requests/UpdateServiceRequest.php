<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'required', 'string', 'max:150'],
            'descricao' => ['sometimes', 'required', 'string', 'max:2000'],
            'categoria' => ['sometimes', 'required', 'string', 'max:100'],
            'valor_sugerido' => ['nullable', 'numeric', 'min:0'],
            'imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'O título é obrigatório.',
            'titulo.max' => 'O título deve ter no máximo 150 caracteres.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição deve ter no máximo 2000 caracteres.',
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.max' => 'A categoria deve ter no máximo 100 caracteres.',
            'valor_sugerido.numeric' => 'O valor sugerido deve ser um número.',
            'valor_sugerido.min' => 'O valor sugerido deve ser maior ou igual a zero.',
            'imagem.image' => 'O arquivo deve ser uma imagem.',
            'imagem.mimes' => 'A imagem deve ser do tipo jpg, jpeg, png ou webp.',
            'imagem.max' => 'A imagem deve ter no máximo 5MB.',
        ];
    }
}
