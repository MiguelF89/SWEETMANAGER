<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendaRequest;
use App\Http\Requests\UpdateVendaRequest;
use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Produto;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index()
    {
        $search = request('search');

        $vendas = Venda::with(['cliente', 'produto'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('cliente', function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%");
                })->orWhereHas('produto', function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        return view('vendas.index', compact('vendas', 'search'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $produtos = Produto::all();
        return view('vendas.create', compact('clientes', 'produtos'));
    }

    public function store(StoreVendaRequest $request)
    {
        $validated = $request->validated();

        $produto = Produto::findOrFail($validated['produto_id']);

        // Calcula o valor total de forma segura no backend
        $validated['valor_total'] = $produto->preco * (float)$validated['quantidade'];
        $validated['quantidade'] = (int)$validated['quantidade'];

        Venda::create($validated);

        return redirect()->route('vendas.index')->with('success', 'Venda criada com sucesso!');
    }

    public function edit(Venda $venda)
    {
        $clientes = Cliente::all();
        $produtos = Produto::all();
        return view('vendas.edit', compact('venda', 'clientes', 'produtos'));
    }

    public function update(UpdateVendaRequest $request, Venda $venda)
    {
        $validated = $request->validated();

        $produto = Produto::findOrFail($validated['produto_id']);

        // Calcula o valor total de forma segura no backend
        $validated['valor_total'] = $produto->preco * (float)$validated['quantidade'];
        $validated['quantidade'] = (int)$validated['quantidade'];

        $venda->update($validated);

        return redirect()->route('vendas.index')->with('success', 'Venda atualizada com sucesso!');
    }

    public function destroy(Venda $venda)
    {
        $venda->delete();
        return redirect()->route('vendas.index')->with('success', 'Venda deletada com sucesso!');
    }
}