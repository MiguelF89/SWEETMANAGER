<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendaRequest extends FormRequest
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
            'instituicao_id' => [
                'required',
                'integer',
                'exists:instituicoes,id',
                function ($attribute, $value, $fail) {
                    // Verifica se a instituição pertence ao usuário logado
                    if (auth()->user()->instituicoes()->where('id', $value)->doesntExist()) {
                        $fail('A instituição selecionada não existe ou não pertence a você.');
                    }
                }
            ],
            'produto_id' => [
                'required',
                'integer',
                'exists:produtos,id',
                function ($attribute, $value, $fail) {
                    // Verifica se o produto pertence ao usuário logado
                    if (auth()->user()->produtos()->where('id', $value)->doesntExist()) {
                        $fail('O produto selecionado não existe ou não pertence a você.');
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
                        $fail('A :attribute deve ser maior que zero.');
                    }
                    if (!is_numeric($value)) {
                        $fail('A :attribute deve ser um número inteiro.');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'instituicao_id.required' => 'A instituição é obrigatória.',
            'instituicao_id.integer' => 'A instituição deve ser um número.',
            'instituicao_id.exists' => 'A instituição selecionada não existe.',
            'produto_id.required' => 'O produto é obrigatório.',
            'produto_id.integer' => 'O produto deve ser um número.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',
            'quantidade.max' => 'A quantidade não pode exceder 999.999.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'instituicao_id' => $this->instituicao_id !== null ? (int)$this->instituicao_id : null,
            'produto_id' => $this->produto_id !== null ? (int)$this->produto_id : null,
            'quantidade' => $this->quantidade !== null ? (int)$this->quantidade : null,
        ]);
    }
}
