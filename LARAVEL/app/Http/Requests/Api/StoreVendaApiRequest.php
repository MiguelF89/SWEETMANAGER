<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendaApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'instituicao_id' => [
                'required',
                'integer',
                'exists:instituicoes,id',
                function ($attribute, $value, $fail) {
                    if (auth()->user()->instituicoes()->where('id', $value)->doesntExist()) {
                        $fail('A instituição informada não existe ou não pertence a você.');
                    }
                }
            ],
            'produto_id' => [
                'required',
                'integer',
                'exists:produtos,id',
                function ($attribute, $value, $fail) {
                    if (auth()->user()->produtos()->where('id', $value)->doesntExist()) {
                        $fail('O produto informado não existe ou não pertence a você.');
                    }
                }
            ],
            'quantidade' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('A quantidade deve ser maior que zero.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'instituicao_id.required' => 'O campo instituição_id é obrigatório.',
            'instituicao_id.integer' => 'O campo instituição_id deve ser um número inteiro.',
            'instituicao_id.exists' => 'A instituição não existe.',
            'produto_id.required' => 'O campo produto_id é obrigatório.',
            'produto_id.integer' => 'O campo produto_id deve ser um número inteiro.',
            'produto_id.exists' => 'O produto não existe.',
            'quantidade.required' => 'O campo quantidade é obrigatório.',
            'quantidade.integer' => 'O campo quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',
            'quantidade.max' => 'A quantidade não pode exceder 999.999.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'instituicao_id' => $this->instituicao_id !== null ? (int)$this->instituicao_id : null,
            'produto_id' => $this->produto_id !== null ? (int)$this->produto_id : null,
            'quantidade' => $this->quantidade !== null ? (int)$this->quantidade : null,
        ]);
    }
}
