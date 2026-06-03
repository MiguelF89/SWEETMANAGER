<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Api\StoreInstituicaoApiRequest;
use App\Http\Requests\Api\UpdateInstituicaoApiRequest;
use App\Http\Controllers\Controller;
use App\Models\Instituicao;
use App\Helpers\ValidacaoBrasileira;
use Illuminate\Http\Request;

class InstituicaoApiController extends Controller
{
    public function index()
    {
        $instituicoes = Instituicao::all()->map(function (Instituicao $inst) {
            return [
                'id'               => $inst->id,
                'nome'             => $inst->nome,
                'cnpj'             => $inst->cnpj,
                'cnpj_formatted'   => $inst->cnpj_formatted,
                'contato'          => $inst->contato,
                'contato_formatted'=> $inst->contato_formatted,
                'user_id'          => $inst->user_id,
            ];
        });

        return response()->json($instituicoes);
    }

    public function store(StoreInstituicaoApiRequest $request)
    {
        $validated = $request->validated();

        // Normaliza dados
        $validated['cnpj'] = ValidacaoBrasileira::normalizarCNPJ($validated['cnpj']);
        $validated['contato'] = ValidacaoBrasileira::normalizarTelefone($validated['contato']);

        $instituicao = Instituicao::create($validated);

        return response()->json([
            'id'               => $instituicao->id,
            'nome'             => $instituicao->nome,
            'cnpj'             => $instituicao->cnpj,
            'cnpj_formatted'   => $instituicao->cnpj_formatted,
            'contato'          => $instituicao->contato,
            'contato_formatted'=> $instituicao->contato_formatted,
            'user_id'          => $instituicao->user_id,
        ], 201);
    }

    public function show($id)
    {
        $id = (int)$id;
        
        $inst = Instituicao::findOrFail($id);

        return response()->json([
            'id'                => $inst->id,
            'nome'              => $inst->nome,
            'cnpj'              => $inst->cnpj,
            'cnpj_formatted'    => $inst->cnpj_formatted,
            'contato'           => $inst->contato,
            'contato_formatted' => $inst->contato_formatted,
            'user_id'           => $inst->user_id,
        ]);
    }

    public function update(UpdateInstituicaoApiRequest $request, $id)
    {
        $id = (int)$id;
        $instituicao = Instituicao::findOrFail($id);

        $validated = $request->validated();

        // Normaliza dados
        $validated['cnpj'] = ValidacaoBrasileira::normalizarCNPJ($validated['cnpj']);
        $validated['contato'] = ValidacaoBrasileira::normalizarTelefone($validated['contato']);

        $instituicao->update($validated);

        return response()->json([
            'id'               => $instituicao->id,
            'nome'             => $instituicao->nome,
            'cnpj'             => $instituicao->cnpj,
            'cnpj_formatted'   => $instituicao->cnpj_formatted,
            'contato'          => $instituicao->contato,
            'contato_formatted'=> $instituicao->contato_formatted,
            'user_id'          => $instituicao->user_id,
        ]);
    }

    public function destroy($id)
    {
        $id = (int)$id;
        $instituicao = Instituicao::findOrFail($id);
        $instituicao->delete();

        return response()->json(['message' => 'Instituição deletada com sucesso'], 200);
    }
}
