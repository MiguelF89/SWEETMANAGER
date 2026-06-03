<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Helpers\ValidacaoBrasileira;

class Telefone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ValidacaoBrasileira::validarTelefone($value)) {
            $fail('O :attribute informado é inválido. Use o formato (XX) XXXXX-XXXX ou (XX) XXXX-XXXX.');
        }
    }
}
