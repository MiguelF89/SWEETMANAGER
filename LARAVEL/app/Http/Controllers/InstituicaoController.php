<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstituicaoRequest;
use App\Http\Requests\UpdateInstituicaoRequest;
use App\Models\Instituicao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\ValidacaoBrasileira;

class InstituicaoController extends Controller
{
    public function index()
    {
        $search = request('search');
        
        $instituicoes = Instituicao::when($search, function ($query) use ($search) {
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
        })->paginate(10);
        
        return view('instituicoes.index', compact('instituicoes', 'search'));
    }

    public function create()
    {
        return view('instituicoes.create');
    }

    public function store(StoreInstituicaoRequest $request)
    {
        $validated = $request->validated();

        // Normaliza CNPJ e telefone antes de salvar
        $validated['cnpj'] = ValidacaoBrasileira::normalizarCNPJ($validated['cnpj']);
        $validated['contato'] = ValidacaoBrasileira::normalizarTelefone($validated['contato']);

        Instituicao::create($validated);

        return redirect()->route('instituicoes.index')->with('success', 'Instituição criada com sucesso!');
    }

    public function edit(Instituicao $instituicao)
    {
        return view('instituicoes.edit', compact('instituicao'));
    }

    public function update(UpdateInstituicaoRequest $request, Instituicao $instituicao)
    {
        $validated = $request->validated();

        // Normaliza CNPJ e telefone antes de salvar
        $validated['cnpj'] = ValidacaoBrasileira::normalizarCNPJ($validated['cnpj']);
        $validated['contato'] = ValidacaoBrasileira::normalizarTelefone($validated['contato']);

        $instituicao->update($validated);

        return redirect()->route('instituicoes.index')->with('success', 'Instituição atualizada com sucesso!');
    }

    public function destroy(Instituicao $instituicao)
    {
        $instituicao->delete();
        return redirect()->route('instituicoes.index')->with('success', 'Instituição deletada com sucesso!');
    }
}
