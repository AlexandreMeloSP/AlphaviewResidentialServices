<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge(['cpf' => preg_replace('/\D/', '', $this->cpf)]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'cpf' => ['required', 'string', 'digits:11', 'unique:users', function (string $attribute, mixed $value, Closure $fail) {
                if (! self::validarCpf((string) $value)) {
                    $fail('O CPF informado é inválido.');
                }
            }],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'max:128', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'],
            'accepted_terms' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'name.regex' => 'O nome deve conter apenas letras e espaços',
            'email.required' => 'O e-mail é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos',
            'password.required' => 'A senha é obrigatória',
            'password.confirmed' => 'A confirmação de senha não confere',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'password.max' => 'A senha deve ter no máximo 128 caracteres.',
            'password.regex' => 'A senha deve conter maiúsculas, minúsculas, números e caracteres especiais',
            'accepted_terms.required' => 'Você precisa aceitar a Política de Privacidade e os Termos de Uso para prosseguir',
            'accepted_terms.accepted' => 'Você precisa aceitar a Política de Privacidade e os Termos de Uso para prosseguir',
            '*.unique' => 'Dados já cadastrados, faça a recuperação de senha ou entre em contato com o administrador.',
        ];
    }

    public static function sanitizarCpf(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf);
    }

    public static function validarCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) return false;
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        if ((int) $cpf[9] !== $digit1) return false;

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        return (int) $cpf[10] === $digit2;
    }
}
