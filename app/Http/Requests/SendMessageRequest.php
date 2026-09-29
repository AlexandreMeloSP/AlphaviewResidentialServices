<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => ['required', 'exists:users,id', function (string $attribute, mixed $value, Closure $fail) {
                if ($this->user() && (int) $value === $this->user()->id) {
                    $fail('Não é possível enviar mensagem para si mesmo.');
                }
            }],
            'content' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_id.required' => 'O destinatário é obrigatório.',
            'receiver_id.exists' => 'O destinatário não foi encontrado.',
            'content.required' => 'O conteúdo da mensagem é obrigatório.',
            'content.max' => 'A mensagem deve ter no máximo 5000 caracteres.',
        ];
    }
}
