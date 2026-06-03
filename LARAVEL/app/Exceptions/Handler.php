<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     * API routes receive structured JSON errors instead of HTML pages.
     */
    public function render($request, Throwable $e)
    {
        // Resposta JSON padronizada para rotas de API
        if ($request->is('api/*') || $request->expectsJson()) {

            // Erro de validação (Form Requests e Validator::make)
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Os dados informados são inválidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // Modelo não encontrado (findOrFail, Route Model Binding)
            if ($e instanceof ModelNotFoundException) {
                $model = class_basename($e->getModel());
                return response()->json([
                    'success' => false,
                    'message' => "Registro não encontrado.",
                ], 404);
            }

            // Rota não encontrada
            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rota não encontrada.',
                ], 404);
            }

            // Método HTTP não permitido
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método HTTP não permitido para esta rota.',
                ], 405);
            }

            // Não autenticado
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado. Faça login para continuar.',
                ], 401);
            }

            // Erro interno genérico (não expõe detalhes em produção)
            return response()->json([
                'success' => false,
                'message' => app()->hasDebugModeEnabled()
                    ? $e->getMessage()
                    : 'Erro interno do servidor.',
            ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
        }

        return parent::render($request, $e);
    }
}
