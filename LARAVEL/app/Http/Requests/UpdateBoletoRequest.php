<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoletoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'descricao' => [
                'required',
                'string',
                'max:255',
                'not_regex:/^\s+$/',
                'min:3',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.max' => 'A descrição não pode exceder 255 caracteres.',
            'descricao.not_regex' => 'A descrição não pode conter apenas espaços.',
            'descricao.min' => 'A descrição deve ter pelo menos 3 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'descricao' => trim($this->descricao ?? ''),
        ]);
    }
}
