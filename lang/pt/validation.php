<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'unique' => 'O valor informado para :attribute já está em uso.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'max' => [
        'string' => 'O campo :attribute não deve exceder :max caracteres.',
    ],
    'regex' => 'O formato do campo :attribute é inválido.',
    'digits' => 'O campo :attribute deve conter :digits dígitos.',
    'accepted' => 'Você precisa aceitar o campo :attribute.',
    'string' => 'O campo :attribute deve ser um texto.',
    'uppercase' => 'O campo :attribute deve conter pelo menos uma letra maiúscula.',
    'lowercase' => 'O campo :attribute deve conter pelo menos uma letra minúscula.',
    'letters' => 'O campo :attribute deve conter pelo menos uma letra.',
    'mixed' => 'O campo :attribute deve conter maiúsculas e minúsculas.',
    'numbers' => 'O campo :attribute deve conter pelo menos um número.',
    'symbols' => 'O campo :attribute deve conter pelo menos um caractere especial.',
    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'cpf' => 'CPF',
        'password' => 'senha',
        'password_confirmation' => 'confirmação de senha',
        'accepted_terms' => 'termos de uso',
    ],
];
