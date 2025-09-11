<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 1);
        $perPage = 10;

    $services = Service::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($services);
    }

    public function list_by_barber(Request $request)
    {
        \Illuminate\Support\Facades\Validator::make($request->all(), ['user_id' => 'required|exists:users,id'], [
            'required' => 'O campo :attribute é obrigatório.',
            'exists' => 'O :attribute informado não existe.'
        ], [
            'user_id' => 'barbeiro'
        ])->validate();

    $services = Service::where('user_id', $request->user_id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'nome' => $service->nome,
                    'preco' => (float)$service->preco,
                    'duracao_minutos' => $service->duracao_minutos
                ];
            });

        return response()->json(['success' => true, 'data' => $services]);
    }

    public function store(StoreServiceRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

    $service = Service::create([
            'user_id' => $user->id,
            'nome' => $data['nome'],
            'preco' => $data['preco'],
            'duracao_minutos' => $data['duracao_minutos'] ?? 30,
            'ativo' => true
        ]);

        return response()->json($service, 201);
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $user = Auth::user();
    $service = Service::where('user_id', $user->id)->findOrFail($id);
        $data = $request->validated();
        $service->update($data);
        return response()->json($service);
    }

    public function toggle_active($id)
    {
        $user = Auth::user();
    $service = Service::where('user_id', $user->id)->findOrFail($id);
        $service->ativo = !$service->ativo;
        $service->save();
        return response()->json($service);
    }
}
