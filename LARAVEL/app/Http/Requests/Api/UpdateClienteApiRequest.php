<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CPF;
use App\Rules\Telefone;

class UpdateClienteApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nome' => [
                'required',
                'string',
                'max:255',
                'not_regex:/^\s+$/',
                function ($attribute, $value, $fail) {
                    if (str_word_count($value) < 2) {
                        $fail('O :attribute deve ter pelo menos 2 palavras.');
                    }
                }
            ],
            'cpf' => [
                'required',
                'string',
                new CPF(),
                'unique:clientes,cpf,' . $this->cliente?->id,
            ],
            'contato' => [
                'required',
                'string',
                new Telefone(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'    => 'O campo nome é obrigatório.',
            'nome.string'      => 'O campo nome deve ser um texto.',
            'nome.max'         => 'O campo nome não pode exceder 255 caracteres.',
            'cpf.required'     => 'O campo CPF é obrigatório.',
            'cpf.string'       => 'O campo CPF deve ser um texto.',
            'cpf.unique'       => 'Este CPF já está cadastrado.',
            'contato.required' => 'O campo contato é obrigatório.',
            'contato.string'   => 'O campo contato deve ser um texto.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome'    => trim($this->nome ?? ''),
            'cpf'     => trim($this->cpf ?? ''),
            'contato' => trim($this->contato ?? ''),
        ]);
    }
}