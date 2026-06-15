<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreEncomendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $linkPagamento = trim($this->link_pagamento ?? '');

        $this->merge([
            'cliente' => trim($this->cliente ?? ''),
            'descricao' => trim($this->descricao ?? ''),
            'observacoes' => trim($this->observacoes ?? ''),
            'link_pagamento' => $linkPagamento ?: null,
            'repassado_cliente' => !empty($linkPagamento),
            'pago' => $this->has('pago'), // Força a existência do campo como true ou false antes de validar
            'quantidade' => $this->quantidade !== null ? (int)$this->quantidade : null,
            'valor' => $this->valor !== null ? (float)$this->valor : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente' => [
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
            'descricao' => [
                'required',
                'string',
                'max:5000',
                'not_regex:/^\s+$/',
                'min:10',
            ],
            'quantidade' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('A :attribute deve ser maior que zero.');
                    }
                }
            ],
            'valor' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('O :attribute deve ser maior que zero.');
                    }
                }
            ],
            'data_entrega' => [
                'required',
                'date',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    try {
                        $data = Carbon::createFromFormat('Y-m-d', $value);
                        $hoje = Carbon::today();

                        // Agora avalia o valor booleano real que foi tratado no prepareForValidation
                        if ($data->lt($hoje) && !$this->input('pago')) {
                            $fail('A data de entrega só pode ser no passado se a encomenda já estiver paga.');
                        }
                    } catch (\Exception $e) {
                        $fail('A data de entrega está em formato inválido.');
                    }
                }
            ],
            'horario_entrega' => [
                'nullable',
                'date_format:H:i',
                'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/',
            ],
            'link_pagamento' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\//',
            ],
            'repassado_cliente' => ['boolean'],
            'pago' => ['boolean'],
            'observacoes' => [
                'nullable',
                'string',
                'max:5000',
                'not_regex:/^\s+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente.required' => 'O nome do cliente é obrigatório.',
            'cliente.string' => 'O nome do cliente deve ser um texto.',
            'cliente.max' => 'O nome do cliente não pode exceder 255 caracteres.',
            'cliente.not_regex' => 'O nome do cliente não pode conter apenas espaços.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.max' => 'A descrição não pode exceder 5000 caracteres.',
            'descricao.not_regex' => 'A descrição não pode conter apenas espaços.',
            'descricao.min' => 'A descrição deve ter pelo menos 10 caracteres.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',
            'quantidade.max' => 'A quantidade não pode exceder 999.999.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser maior que R$ 0,00.',
            'valor.max' => 'O valor não pode exceder R$ 999.999,99.',
            'valor.regex' => 'O valor deve ter no máximo 2 casas decimais.',
            'data_entrega.required' => 'A data de entrega é obrigatória.',
            'data_entrega.date' => 'A data de entrega deve ser uma data válida.',
            'data_entrega.date_format' => 'A data de entrega deve estar no formato YYYY-MM-DD.',
            'horario_entrega.date_format' => 'O horário deve estar no formato HH:MM.',
            'horario_entrega.regex' => 'O horário deve ser válido (00:00 a 23:59).',
            'link_pagamento.url' => 'O link de pagamento deve ser uma URL válida.',
            'link_pagamento.max' => 'O link de pagamento não pode exceder 255 caracteres.',
            'link_pagamento.regex' => 'O link deve começar com http:// ou https://.',
            'observacoes.max' => 'As observações não podem exceder 5000 caracteres.',
            'observacoes.not_regex' => 'As observações não podem conter apenas espaços.',
        ];
    }
}