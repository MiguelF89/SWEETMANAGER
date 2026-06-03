<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CNPJ;
use App\Rules\Telefone;

class StoreInstituicaoApiRequest extends FormRequest
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
            'cnpj' => [
                'required',
                'string',
                new CNPJ(),
                'unique:instituicoes,cnpj',
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
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo nome deve ser um texto.',
            'nome.max' => 'O campo nome não pode exceder 255 caracteres.',
            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.string' => 'O campo CNPJ deve ser um texto.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'contato.required' => 'O campo contato é obrigatório.',
            'contato.string' => 'O campo contato deve ser um texto.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => trim($this->nome ?? ''),
            'cnpj' => trim($this->cnpj ?? ''),
            'contato' => trim($this->contato ?? ''),
        ]);
    }
}
