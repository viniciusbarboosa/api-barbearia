<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicoController extends Controller
{
    public function listar(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 1);
        $perPage = 10;

        $servicos = Servico::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($servicos);
    }

    public function listarBarbeiro(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $servicos = Servico::where('user_id', $request->user_id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(function ($servico) {
                return [
                    'id' => $servico->id,
                    'nome' => $servico->nome,
                    'preco' => (float)$servico->preco, 
                    'duracao_minutos' => $servico->duracao_minutos
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $servicos
        ]);
    }


    public function create(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'duracao_minutos' => 'nullable|integer|min:1'
        ]);

        $servico = Servico::create([
            'user_id' => $user->id, // Automaticamente associa ao usuário logado
            'nome' => $request->nome,
            'preco' => $request->preco,
            'duracao_minutos' => $request->duracao_minutos ?? 30,
            'ativo' => true
        ]);

        return response()->json($servico, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $servico = Servico::where('user_id', $user->id) //USUARIO LOGADO
            ->findOrFail($id);

        $request->validate([
            'nome' => 'string|max:100',
            'preco' => 'numeric|min:0.01',
            'duracao_minutos' => 'nullable|integer|min:1',
            'ativo' => 'boolean'
        ]);

        $servico->update($request->all());
        return response()->json($servico);
    }

    public function toggleAtivo($id)
    {
        $user = Auth::user();
        $servico = Servico::where('user_id', $user->id)
            ->findOrFail($id);

        $servico->ativo = !$servico->ativo;
        $servico->save();
        return response()->json($servico);
    }
}
