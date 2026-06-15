<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Api\StoreVendaApiRequest;
use App\Http\Requests\Api\UpdateVendaApiRequest;
use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\Produto;
use Illuminate\Http\Request;

class VendaApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Venda::with(['cliente', 'produto'])->get()
        );
    }

    public function store(StoreVendaApiRequest $request)
    {
        $validated = $request->validated();

        $produto = Produto::findOrFail($validated['produto_id']);

        $validated['valor_total'] = $produto->preco * (float)$validated['quantidade'];
        $validated['quantidade'] = (int)$validated['quantidade'];

        $venda = Venda::create($validated);

        return response()->json($venda, 201);
    }

    public function show($id)
    {
        $id = (int)$id;
        $venda = Venda::with(['cliente', 'produto'])->findOrFail($id);

        return response()->json($venda);
    }

    public function update(UpdateVendaApiRequest $request, $id)
    {
        $id = (int)$id;
        $venda = Venda::findOrFail($id);

        $validated = $request->validated();

        $produto = Produto::findOrFail($validated['produto_id']);

        $validated['valor_total'] = $produto->preco * (float)$validated['quantidade'];
        $validated['quantidade'] = (int)$validated['quantidade'];

        $venda->update($validated);

        return response()->json($venda);
    }

    public function destroy($id)
    {
        $id = (int)$id;
        $venda = Venda::findOrFail($id);
        $venda->delete();

        return response()->json(['message' => 'Venda deletada com sucesso'], 200);
    }
}