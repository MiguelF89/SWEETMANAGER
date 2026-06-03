<?php

namespace App\Helpers;

class ValidacaoBrasileira
{
    /**
     * Valida e normaliza um CPF
     * Remove caracteres especiais e valida o dígito verificador
     * 
     * @param string $cpf
     * @return bool
     */
    public static function validarCPF(string $cpf): bool
    {
        // Remove caracteres não-numéricos
        $cpf = preg_replace('/\D/', '', $cpf);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se não é uma sequência repetida (ex: 111.111.111-11)
        if (preg_match('/^' . $cpf[0] . '{11}$/', $cpf)) {
            return false;
        }

        // Calcula primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $cpf[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $dv1 = $resto < 2 ? 0 : 11 - $resto;

        // Calcula segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += $cpf[$i] * (11 - $i);
        }
        $resto = $soma % 11;
        $dv2 = $resto < 2 ? 0 : 11 - $resto;

        // Verifica se os dígitos verificadores estão corretos
        return ($cpf[9] == $dv1 && $cpf[10] == $dv2);
    }

    /**
     * Normaliza um CPF para o formato XXX.XXX.XXX-XX
     * 
     * @param string $cpf
     * @return string|null
     */
    public static function normalizarCPF(string $cpf): ?string
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return null;
        }

        return substr($cpf, 0, 3) . '.' . 
               substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . 
               substr($cpf, 9, 2);
    }

    /**
     * Valida e normaliza um CNPJ
     * Remove caracteres especiais e valida o dígito verificador
     * 
     * @param string $cnpj
     * @return bool
     */
    public static function validarCNPJ(string $cnpj): bool
    {
        // Remove caracteres não-numéricos
        $cnpj = preg_replace('/\D/', '', $cnpj);

        // Verifica se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se não é uma sequência repetida (ex: 11.111.111/1111-11)
        if (preg_match('/^' . $cnpj[0] . '{14}$/', $cnpj)) {
            return false;
        }

        // Calcula primeiro dígito verificador
        $t = 5;
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $t;
            $t = $t == 2 ? 9 : $t - 1;
        }
        $dv1 = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);

        // Calcula segundo dígito verificador
        $t = 6;
        $soma = 0;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $t;
            $t = $t == 2 ? 9 : $t - 1;
        }
        $dv2 = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);

        // Verifica se os dígitos verificadores estão corretos
        return ($cnpj[12] == $dv1 && $cnpj[13] == $dv2);
    }

    /**
     * Normaliza um CNPJ para o formato XX.XXX.XXX/XXXX-XX
     * 
     * @param string $cnpj
     * @return string|null
     */
    public static function normalizarCNPJ(string $cnpj): ?string
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return null;
        }

        return substr($cnpj, 0, 2) . '.' . 
               substr($cnpj, 2, 3) . '.' . 
               substr($cnpj, 5, 3) . '/' . 
               substr($cnpj, 8, 4) . '-' . 
               substr($cnpj, 12, 2);
    }

    /**
     * Valida um telefone brasileiro (celular ou fixo)
     * Aceita formatos: (XX) XXXXX-XXXX, (XX) XXXX-XXXX, XX XXXXX XXXX, etc
     * 
     * @param string $telefone
     * @return bool
     */
    public static function validarTelefone(string $telefone): bool
    {
        // Remove caracteres não-numéricos
        $telefone = preg_replace('/\D/', '', $telefone);

        // Deve ter 10 ou 11 dígitos
        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            return false;
        }

        // DDD deve ser válido (11 a 99)
        $ddd = (int)substr($telefone, 0, 2);
        if ($ddd < 11 || $ddd > 99) {
            return false;
        }

        return true;
    }

    /**
     * Normaliza um telefone para o formato (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
     * 
     * @param string $telefone
     * @return string|null
     */
    public static function normalizarTelefone(string $telefone): ?string
    {
        $telefone = preg_replace('/\D/', '', $telefone);

        if (strlen($telefone) == 11) {
            // Celular: (XX) XXXXX-XXXX
            return '(' . substr($telefone, 0, 2) . ') ' . 
                   substr($telefone, 2, 5) . '-' . 
                   substr($telefone, 7, 4);
        } elseif (strlen($telefone) == 10) {
            // Fixo: (XX) XXXX-XXXX
            return '(' . substr($telefone, 0, 2) . ') ' . 
                   substr($telefone, 2, 4) . '-' . 
                   substr($telefone, 6, 4);
        }

        return null;
    }

    /**
     * Valida um CEP brasileiro
     * Formato: XXXXX-XXX ou XXXXXXXX
     * 
     * @param string $cep
     * @return bool
     */
    public static function validarCEP(string $cep): bool
    {
        $cep = preg_replace('/\D/', '', $cep);

        // Deve ter 8 dígitos
        if (strlen($cep) != 8) {
            return false;
        }

        // CEP não deve ser uma sequência repetida
        if (preg_match('/^' . $cep[0] . '{8}$/', $cep)) {
            return false;
        }

        return true;
    }

    /**
     * Normaliza um CEP para o formato XXXXX-XXX
     * 
     * @param string $cep
     * @return string|null
     */
    public static function normalizarCEP(string $cep): ?string
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) != 8) {
            return null;
        }

        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }

    /**
     * Obtém apenas os dígitos de uma string
     * 
     * @param string $string
     * @return string
     */
    public static function somenteDigitos(string $string): string
    {
        return preg_replace('/\D/', '', $string);
    }
}
