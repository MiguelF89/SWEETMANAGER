<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Cliente;
use App\Models\Produto;

class UpdateVendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => [
                'required',
                'integer',
                'exists:clientes,id',
                function ($attribute, $value, $fail) {
                    // CORREÇÃO: GlobalScope do Cliente já filtra por user_id logado.
                    if (Cliente::where('id', $value)->doesntExist()) {
                        $fail('O cliente selecionado não existe ou não pertence a você.');
                    }
                },
            ],
            'produto_id' => [
                'required',
                'integer',
                'exists:produtos,id',
                function ($attribute, $value, $fail) {
                    // CORREÇÃO: GlobalScope do Produto já filtra por user_id logado.
                    if (Produto::where('id', $value)->doesntExist()) {
                        $fail('O produto selecionado não existe ou não pertence a você.');
                    }
                },
            ],
            'quantidade' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'O cliente é obrigatório.',
            'cliente_id.integer'  => 'O cliente deve ser um número.',
            'cliente_id.exists'   => 'O cliente selecionado não existe.',
            'produto_id.required' => 'O produto é obrigatório.',
            'produto_id.integer'  => 'O produto deve ser um número.',
            'produto_id.exists'   => 'O produto selecionado não existe.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer'  => 'A quantidade deve ser um número inteiro.',
            'quantidade.min'      => 'A quantidade deve ser maior que zero.',
            'quantidade.max'      => 'A quantidade não pode exceder 999.999.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cliente_id' => $this->cliente_id !== null ? (int) $this->cliente_id : null,
            'produto_id' => $this->produto_id !== null ? (int) $this->produto_id : null,
            'quantidade' => $this->quantidade  !== null ? (int) $this->quantidade  : null,
        ]);
    }
}