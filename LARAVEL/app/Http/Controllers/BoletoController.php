<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoletoReadRequest;
use App\Http\Requests\PagarBoletoRequest;
use App\Http\Requests\UpdateBoletoRequest;
use App\Models\Boleto;
use App\Services\BoletoReaderService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoletoController extends Controller
{
    // ── Leitor (página) ────────────────────────────────────────
    public function reader()
    {
        return view('boleto.reader');
    }

    // ── Lê o arquivo e SALVA automaticamente na lista ──────────
    public function read(BoletoReadRequest $request): JsonResponse
    {
        try {
            $service = app(BoletoReaderService::class);
            $data = $service->read($request->file('file'));

            // Validações adicionais dos dados extraídos
            if (!isset($data['amount']) || $data['amount'] <= 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'O boleto deve ter um valor maior que zero.',
                ], 422);
            }

            if (!isset($data['due_date'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Não foi possível extrair a data de vencimento do boleto.',
                ], 422);
            }

            // Valida data de vencimento
            try {
                $vencimento = Carbon::createFromFormat('Y-m-d', $data['due_date']);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Data de vencimento inválida.',
                ], 422);
            }

            // Salva automaticamente na tabela de boletos
            $boleto = Boleto::create([
                'barcode'         => $data['barcode'] ?? null,
                'linha_digitavel' => $data['linha_digitavel'] ?? null,
                'valor'           => (float)$data['amount'],
                'vencimento'      => $vencimento,
                'banco'           => $data['bank'] ?? null,
                'descricao'       => 'Boleto importado automaticamente',
                'status'          => 'pendente',
                'user_id'         => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'data'    => array_merge($data, ['id' => $boleto->id]),
                'message' => 'Boleto lido e adicionado à lista de pagamentos.',
            ]);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'error' => 'Erro interno ao processar boleto.'], 500);
        }
    }

    // ── Lista de boletos ───────────────────────────────────────
    public function index(Request $request)
    {
        $status = $request->get('status', 'todos');
        
        // Valida parâmetro status
        if (!in_array($status, ['todos', 'pendente', 'pago', 'vencido'])) {
            $status = 'todos';
        }

        $query = Boleto::orderBy('vencimento');

        if ($status === 'pendente') {
            $query->where('status', 'pendente');
        } elseif ($status === 'pago') {
            $query->where('status', 'pago');
        } elseif ($status === 'vencido') {
            $query->where('status', 'vencido');
        }

        $boletos = $query->paginate(15)->withQueryString();

        $totais = [
            'pendente' => Boleto::where('status', 'pendente')->sum('valor'),
            'pago'     => Boleto::where('status', 'pago')->sum('valor'),
            'total'    => Boleto::sum('valor'),
        ];

        return view('boleto.index', compact('boletos', 'totais', 'status'));
    }

    // ── Marcar como pago ───────────────────────────────────────
    public function pagar(PagarBoletoRequest $request, Boleto $boleto): JsonResponse
    {
        $validated = $request->validated();

        $boleto->update([
            'status'         => 'pago',
            'data_pagamento' => $validated['data_pagamento'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Boleto marcado como pago.',
        ]);
    }

    // ── Editar descrição ───────────────────────────────────────
    public function update(UpdateBoletoRequest $request, Boleto $boleto): JsonResponse
    {
        $validated = $request->validated();

        $boleto->update([
            'descricao' => $validated['descricao'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Descrição atualizada com sucesso.',
        ]);
    }

    // ── Excluir ────────────────────────────────────────────────
    public function destroy(Boleto $boleto): JsonResponse
    {
        $boleto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Boleto deletado com sucesso.',
        ]);
    }
}