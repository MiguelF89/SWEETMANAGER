<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class PagarBoletoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $today = Carbon::today()->format('Y-m-d');
        
        return [
            'data_pagamento' => [
                'required',
                'date',
                'date_format:Y-m-d',
                "before_or_equal:$today",
                function ($attribute, $value, $fail) {
                    try {
                        $data = Carbon::createFromFormat('Y-m-d', $value);
                        if ($data->isFuture()) {
                            $fail('A data de pagamento não pode ser no futuro.');
                        }
                    } catch (\Exception $e) {
                        $fail('A data de pagamento está em formato inválido.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'data_pagamento.required' => 'A data de pagamento é obrigatória.',
            'data_pagamento.date' => 'A data de pagamento deve ser uma data válida.',
            'data_pagamento.date_format' => 'A data deve estar no formato YYYY-MM-DD.',
            'data_pagamento.before_or_equal' => 'A data de pagamento não pode ser no futuro.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'data_pagamento' => $this->data_pagamento ?? Carbon::today()->format('Y-m-d'),
        ]);
    }
}
