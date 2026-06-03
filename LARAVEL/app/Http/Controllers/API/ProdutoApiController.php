<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Api\StoreProdutoApiRequest;
use App\Http\Requests\Api\UpdateProdutoApiRequest;
use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoApiController extends Controller
{
    public function index()
    {
        return response()->json(Produto::all());
    }

    public function store(StoreProdutoApiRequest $request)
    {
        $validated = $request->validated();
        $validated['preco'] = (float)$validated['preco'];

        $produto = Produto::create($validated);

        return response()->json($produto, 201);
    }

    public function show($id)
    {
        $id = (int)$id;
        $produto = Produto::findOrFail($id);
        
        return response()->json($produto);
    }

    public function update(UpdateProdutoApiRequest $request, $id)
    {
        $id = (int)$id;
        $produto = Produto::findOrFail($id);
        
        $validated = $request->validated();
        $validated['preco'] = (float)$validated['preco'];
        
        $produto->update($validated);

        return response()->json($produto);
    }

    public function destroy($id)
    {
        $id = (int)$id;
        $produto = Produto::findOrFail($id);
        $produto->delete();

        return response()->json(['message' => 'Produto deletado com sucesso'], 200);
    }
}
