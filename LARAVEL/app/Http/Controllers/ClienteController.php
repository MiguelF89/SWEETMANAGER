<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ValidacaoBrasileira;

class ClienteController extends Controller
{
    public function index()
    {
        $search = request('search');

        $clientes = Cliente::when($search, function ($query) use ($search) {
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
        })->paginate(10);

        return view('clientes.index', compact('clientes', 'search'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $validated = $request->validated();

        // Normaliza CPF e telefone antes de salvar
        $validated['cpf'] = ValidacaoBrasileira::normalizarCPF($validated['cpf']);
        $validated['contato'] = ValidacaoBrasileira::normalizarTelefone($validated['contato']);

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente criado com sucesso!');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $validated = $request->validated();

        // Normaliza CPF e telefone antes de salvar
        $validated['cpf'] = ValidacaoBrasileira::normalizarCPF($validated['cpf']);
        $validated['contato'] = ValidacaoBrasileira::normalizarTelefone($validated['contato']);

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente deletado com sucesso!');
    }
}