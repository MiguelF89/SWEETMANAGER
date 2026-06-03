<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdutoApiRequest extends FormRequest
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
                        $fail('O campo nome deve ter pelo menos 2 palavras.');
                    }
                }
            ],
            'preco' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('O campo preço deve ser maior que zero.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo nome deve ser um texto.',
            'nome.max' => 'O campo nome não pode exceder 255 caracteres.',
            'preco.required' => 'O campo preço é obrigatório.',
            'preco.numeric' => 'O campo preço deve ser um número.',
            'preco.min' => 'O campo preço deve ser maior que R$ 0,00.',
            'preco.max' => 'O campo preço não pode exceder R$ 999.999,99.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => trim($this->nome ?? ''),
            'preco' => $this->preco !== null ? (float)$this->preco : null,
        ]);
    }
}
