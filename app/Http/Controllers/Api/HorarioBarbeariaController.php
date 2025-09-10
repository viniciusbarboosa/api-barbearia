<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\HorarioBarbearia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HorarioBarbeariaController extends Controller
{
    public function criarHorarios(Request $request)
    {
        $request->validate([
            'data' => 'required|date|after_or_equal:today',
            'horario_inicio_expediente' => 'required|date_format:H:i',
            'horario_fim_expediente' => 'required|date_format:H:i|after:horario_inicio_expediente'
        ]);

        $data = $request->data;
        $inicioExpediente = Carbon::parse($request->horario_inicio_expediente);
        $fimExpediente = Carbon::parse($request->horario_fim_expediente);
        $user_id = Auth::id();

        HorarioBarbearia::where('user_id', $user_id)
            ->where('data', $data)
            ->delete();

        $horariosCriados = [];
        $horarioAtual = $inicioExpediente->copy();

        while ($horarioAtual <= $fimExpediente) {
            $horario = HorarioBarbearia::create([
                'user_id' => $user_id,
                'data' => $data,
                'horario_inicio' => $horarioAtual->format('H:i:s'),
                'horario_fim' => $horarioAtual->addMinutes(15)->format('H:i:s'),
                'disponivel' => true
            ]);

            $horariosCriados[] = $horario;
        }

        return response()->json([
            'message' => 'Horários criados com sucesso',
            'horarios' => $horariosCriados
        ], 201);
    }

    public function listarPorData(Request $request)
    {
        $request->validate([
            'data' => 'required|date'
        ]);

        $horarios = HorarioBarbearia::where('user_id', Auth::id())
            ->where('data', $request->data)
            ->orderBy('horario_inicio')
            ->get();

        return response()->json($horarios);
    }

    public function toggleDisponibilidade(Request $request, $id)
    {
        $request->validate([
            'disponivel' => 'required|boolean'
        ]);

        $horario = HorarioBarbearia::where('user_id', Auth::id())
            ->findOrFail($id);

        $horario->update(['disponivel' => $request->disponivel]);

        return response()->json($horario);
    }

    //PART DO HORARIO DO AGENDAMENTO
    // REGRAS
    public function listarHorarios($barbeiroId, Request $request)
    {
        try {
            $request->validate([
                'data' => 'required|date_format:Y-m-d'
            ]);

            $horarios = HorarioBarbearia::where('user_id', $barbeiroId)
                ->where('data', $request->data)
                ->orderBy('horario_inicio')
                ->get()
                ->map(function ($horario) {
                    $agendado = Agendamento::where('horario_id', $horario->id)
                        ->where('data', $horario->data)
                        ->exists();

                    return [
                        'id' => $horario->id,
                        'horario_inicio' => $horario->horario_inicio,
                        'horario_fim' => $horario->horario_fim,
                        'data' => $horario->data,
                        'disponivel' => $horario->disponivel && !$agendado,
                        'agendado' => $agendado
                    ];
                });

            return response()->json($horarios);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar horários',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
