<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Api\StoreClienteApiRequest;
use App\Http\Requests\Api\UpdateClienteApiRequest;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Helpers\BrasilHelper;
use Illuminate\Http\Request;

class ClienteApiController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all()->map(function (Cliente $cliente) {
            return [
                'id'                => $cliente->id,
                'nome'              => $cliente->nome,
                'cpf'               => $cliente->cpf,
                'cpf_formatted'     => $cliente->cpf_formatted,
                'contato'           => $cliente->contato,
                'contato_formatted' => $cliente->contato_formatted,
                'user_id'           => $cliente->user_id,
            ];
        });

        return response()->json($clientes);
    }

    public function store(StoreClienteApiRequest $request)
    {
        $validated = $request->validated();

        // Normaliza dados usando o novo BrasilHelper
        $validated['cpf'] = BrasilHelper::normalizeCpf($validated['cpf']);
        $validated['contato'] = BrasilHelper::normalizePhone($validated['contato']);

        $cliente = Cliente::create($validated);

        return response()->json([
            'id'                => $cliente->id,
            'nome'              => $cliente->nome,
            'cpf'               => $cliente->cpf,
            'cpf_formatted'     => $cliente->cpf_formatted,
            'contato'           => $cliente->contato,
            'contato_formatted' => $cliente->contato_formatted,
            'user_id'           => $cliente->user_id,
        ], 201);
    }

    public function show($id)
    {
        $id = (int)$id;

        $cliente = Cliente::findOrFail($id);

        return response()->json([
            'id'                => $cliente->id,
            'nome'              => $cliente->nome,
            'cpf'               => $cliente->cpf,
            'cpf_formatted'     => $cliente->cpf_formatted,
            'contato'           => $cliente->contato,
            'contato_formatted' => $cliente->contato_formatted,
            'user_id'           => $cliente->user_id,
        ]);
    }

    public function update(UpdateClienteApiRequest $request, $id)
    {
        $id = (int)$id;
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validated();

        // Normaliza dados usando o novo BrasilHelper
        $validated['cpf'] = BrasilHelper::normalizeCpf($validated['cpf']);
        $validated['contato'] = BrasilHelper::normalizePhone($validated['contato']);

        $cliente->update($validated);

        return response()->json([
            'id'                => $cliente->id,
            'nome'              => $cliente->nome,
            'cpf'               => $cliente->cpf,
            'cpf_formatted'     => $cliente->cpf_formatted,
            'contato'           => $cliente->contato,
            'contato_formatted' => $cliente->contato_formatted,
            'user_id'           => $cliente->user_id,
        ]);
    }

    public function destroy($id)
    {
        $id = (int)$id;
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return response()->json(['message' => 'Cliente deletado com sucesso'], 200);
    }
}