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
            ->where('active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => (float)$service->price,
                    'duration_minutes' => $service->duration_minutes
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
            'name' => $data['name'] ?? $data['nome'] ?? null,
            'price' => $data['price'] ?? $data['preco'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? $data['duracao_minutos'] ?? 30,
            'active' => true
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
    $service->active = !$service->active;
        $service->save();
        return response()->json($service);
    }
}
