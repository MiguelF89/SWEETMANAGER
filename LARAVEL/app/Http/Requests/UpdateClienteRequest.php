<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CPF;
use App\Rules\Telefone;

class UpdateClienteRequest extends FormRequest
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
                'unique:clientes,cpf,' . $this->cliente->id,
            ],
            'contato' => [
                'required',
                'string',
                new Telefone(),
                'regex:/^[\d\s\-\(\)]+$/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto.',
            'nome.max' => 'O nome não pode exceder 255 caracteres.',
            'nome.not_regex' => 'O nome não pode conter apenas espaços.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser um texto.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'contato.required' => 'O telefone é obrigatório.',
            'contato.string' => 'O telefone deve ser um texto.',
            'contato.regex' => 'O telefone contém caracteres inválidos.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => trim($this->nome ?? ''),
            'cpf' => trim($this->cpf ?? ''),
            'contato' => trim($this->contato ?? ''),
        ]);
    }
}
