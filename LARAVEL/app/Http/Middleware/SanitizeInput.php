<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de sanitização de entradas.
 *
 * - Remove tags HTML/PHP de todos os campos de texto.
 * - Não altera senhas, tokens, arquivos ou campos binários.
 * - Aplica strip_tags + htmlspecialchars para prevenir XSS.
 */
class SanitizeInput
{
    /**
     * Campos que nunca devem ser sanitizados.
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->except($this->except);

        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Remove tags HTML e PHP
                $value = strip_tags($value);
                // Converte entidades HTML especiais (previne XSS)
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
