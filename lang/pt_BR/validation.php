<?php

return [
    'accepted'             => 'O campo :attribute deve ser aceito.',
    'active_url'           => 'O campo :attribute não é uma URL válida.',
    'after'                => 'O campo :attribute deve ser uma data posterior a :date.',
    'alpha'                => 'O campo :attribute deve conter apenas letras.',
    'alpha_dash'           => 'O campo :attribute deve conter apenas letras, números, traços e sublinhados.',
    'alpha_num'            => 'O campo :attribute deve conter apenas letras e números.',
    'array'                => 'O campo :attribute deve ser um array.',
    'before'               => 'O campo :attribute deve ser uma data anterior a :date.',
    'confirmed'            => 'A confirmação do campo :attribute não confere.',
    'email'                => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'ends_with'            => 'O campo :attribute deve terminar com um dos seguintes valores: :values.',
    'exists'               => 'O :attribute selecionado é inválido.',
    'file'                 => 'O campo :attribute deve ser um arquivo.',
    'filled'               => 'O campo :attribute é obrigatório.',
    'image'                => 'O campo :attribute deve ser uma imagem.',
    'in'                   => 'O :attribute selecionado é inválido.',
    'integer'              => 'O campo :attribute deve ser um número inteiro.',
    'max'                  => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'file'    => 'O campo :attribute não pode ser maior que :max kilobytes.',
        'string'  => 'O campo :attribute não pode ser maior que :max caracteres.',
    ],
    'mimes'                => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min'                  => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'file'    => 'O campo :attribute deve ter pelo menos :min kilobytes.',
        'string'  => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'numeric'              => 'O campo :attribute deve ser um número.',
    'required'             => 'O campo :attribute é obrigatório.',
    'same'                 => 'Os campos :attribute e :other devem ser iguais.',
    'size'                 => [
        'numeric' => 'O campo :attribute deve ser :size.',
        'file'    => 'O campo :attribute deve ter :size kilobytes.',
        'string'  => 'O campo :attribute deve ter :size caracteres.',
    ],
    'string'               => 'O campo :attribute deve ser uma string.',
    'unique'               => 'Este :attribute já está em uso.',
    'uploaded'             => 'Falha no upload do campo :attribute.',

    /*
    |--------------------------------------------------------------------------
    | Nomes dos Atributos Personalizados
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name'                  => 'nome',
        'username'              => 'nome de usuário',
        'email'                 => 'e-mail',
        'password'              => 'senha',
        'password_confirmation' => 'confirmação da senha',
        'title'                 => 'título',
        'location'              => 'localização',
        'image'                 => 'imagem',
        'avatar'                => 'foto de perfil',
        'role'                  => 'perfil',
    ],
];