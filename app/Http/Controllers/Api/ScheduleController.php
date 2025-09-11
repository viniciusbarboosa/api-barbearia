<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\HorarioBarbearia;
use App\Http\Requests\CreateScheduleRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function create_schedules(CreateScheduleRequest $request)
    {
        $data = $request->validated();

        $date = $data['data'];
        $start = Carbon::parse($request->horario_inicio_expediente);
        $end = Carbon::parse($request->horario_fim_expediente);
        $user_id = Auth::id();

        HorarioBarbearia::where('user_id', $user_id)->where('data', $date)->delete();

        $created = [];
        $current = $start->copy();

        while ($current <= $end) {
            $horario = HorarioBarbearia::create([
                'user_id' => $user_id,
                'data' => $date,
                'horario_inicio' => $current->format('H:i:s'),
                'horario_fim' => $current->addMinutes(15)->format('H:i:s'),
                'disponivel' => true
            ]);

            $created[] = $horario;
        }

        return response()->json(['message' => 'Horários criados com sucesso', 'horarios' => $created], 201);
    }

    public function list_by_date(Request $request)
    {
        $request->validate(['data' => 'required|date']);

        $schedules = HorarioBarbearia::where('user_id', Auth::id())->where('data', $request->data)->orderBy('horario_inicio')->get();

        return response()->json($schedules);
    }

    public function toggle_availability(Request $request, $id)
    {
        $request->validate(['disponivel' => 'required|boolean']);

        $schedule = HorarioBarbearia::where('user_id', Auth::id())->findOrFail($id);
        $schedule->update(['disponivel' => $request->disponivel]);

        return response()->json($schedule);
    }

    public function list_schedules($barberId, Request $request)
    {
        try {
            $request->validate(['data' => 'required|date_format:Y-m-d']);

            $schedules = HorarioBarbearia::where('user_id', $barberId)->where('data', $request->data)->orderBy('horario_inicio')->get()->map(function ($schedule) {
                $booked = Agendamento::where('horario_id', $schedule->id)->where('data', $schedule->data)->exists();

                return [
                    'id' => $schedule->id,
                    'horario_inicio' => $schedule->horario_inicio,
                    'horario_fim' => $schedule->horario_fim,
                    'data' => $schedule->data,
                    'disponivel' => $schedule->disponivel && !$booked,
                    'agendado' => $booked
                ];
            });

            return response()->json($schedules);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar horários', 'details' => $e->getMessage()], 500);
        }
    }
}
