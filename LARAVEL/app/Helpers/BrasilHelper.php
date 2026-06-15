<?php

namespace App\Helpers;

class BrasilHelper
{
    // ─── CPF ─────────────────────────────────────────────────────────────────

    /**
     * Remove tudo que não for dígito.
     */
    public static function normalizeCpf(?string $cpf): string
    {
        return preg_replace('/\D/', '', (string) $cpf);
    }

    /**
     * Formata dígitos para 000.000.000-00.
     * Retorna o valor original se não tiver 11 dígitos.
     */
    public static function formatCpf(?string $cpf): string
    {
        $digits = self::normalizeCpf($cpf);

        if (strlen($digits) !== 11) {
            return (string) $cpf;
        }

        return substr($digits, 0, 3) . '.' .
               substr($digits, 3, 3) . '.' .
               substr($digits, 6, 3) . '-' .
               substr($digits, 9, 2);
    }

    /**
     * Valida estruturalmente o CPF (dígitos verificadores).
     */
    public static function validateCpf(?string $cpf): bool
    {
        $digits = self::normalizeCpf($cpf);

        if (strlen($digits) !== 11) {
            return false;
        }

        // Rejeita sequências idênticas conhecidas (111.111.111-11, etc.)
        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        // Primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $digits[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $first = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $digits[9] !== $first) {
            return false;
        }

        // Segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $digits[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $second = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $digits[10] === $second;
    }

    // ─── Telefone ─────────────────────────────────────────────────────────────

    /**
     * Remove tudo que não for dígito.
     */
    public static function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D/', '', (string) $phone);
    }

    /**
     * Formata automaticamente:
     * 11 dígitos → (XX) XXXXX-XXXX  (celular)
     * 10 dígitos → (XX) XXXX-XXXX   (fixo)
     * Retorna o valor original para outros tamanhos.
     */
    public static function formatPhone(?string $phone): string
    {
        $digits = self::normalizePhone($phone);

        if (strlen($digits) === 11) {
            return '(' . substr($digits, 0, 2) . ') ' .
                   substr($digits, 2, 5) . '-' .
                   substr($digits, 7, 4);
        }

        if (strlen($digits) === 10) {
            return '(' . substr($digits, 0, 2) . ') ' .
                   substr($digits, 2, 4) . '-' .
                   substr($digits, 6, 4);
        }

        return (string) $phone;
    }

    /**
     * Verifica se o número de dígitos é válido para BR (10 ou 11).
     */
    public static function validatePhone(?string $phone): bool
    {
        $digits = self::normalizePhone($phone);
        return in_array(strlen($digits), [10, 11], true);
    }
}