<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\HorarioBarbearia;
use App\Models\Servico;
use App\Models\User;
use App\Http\Requests\CreateAppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(CreateAppointmentRequest $request)
    {
        $data = $request->validated();

        $servico = Servico::find($data['servico_id']);
        if (!$servico) {
            return response()->json(['message' => 'Serviço não encontrado'], 404);
        }

        $initialSchedule = HorarioBarbearia::find($data['horario_id']);
        if (!$initialSchedule) {
            return response()->json(['message' => 'Horário inicial não encontrado'], 404);
        }

        $blocksNeeded = ceil($servico->duracao_minutos / 15);

        if ($blocksNeeded == 1) {
            if (!$initialSchedule->disponivel) {
                return response()->json(['message' => 'Horário já está ocupado'], 400);
            }
        } else {
            $daySchedules = HorarioBarbearia::where('user_id', $data['barbeiro_id'])->where('data', $data['data'])->orderBy('horario_inicio')->get();

            $startIndex = null;
            $schedulesToBook = [];

            foreach ($daySchedules as $index => $schedule) {
                if ($schedule->id == $initialSchedule->id) {
                    $startIndex = $index;
                    break;
                }
            }

            if ($startIndex === null) {
                return response()->json(['message' => 'Horário inicial não encontrado na lista do dia'], 404);
            }

            for ($i = 0; $i < $blocksNeeded; $i++) {
                $currentIndex = $startIndex + $i;
                if (!isset($daySchedules[$currentIndex])) {
                    return response()->json(['message' => 'Tempo insuficiente para este serviço'], 400);
                }

                $current = $daySchedules[$currentIndex];
                if (!$current->disponivel) {
                    return response()->json(['message' => 'Horário já está ocupado'], 400);
                }

                if ($i > 0) {
                    $prev = $daySchedules[$currentIndex - 1];
                    if ($prev->horario_fim != $current->horario_inicio) {
                        return response()->json(['message' => 'Horários não são consecutivos'], 400);
                    }
                }

                $schedulesToBook[] = $current;
            }
        }

        $appointment = Agendamento::create([
            'user_id' => Auth::id(),
            'barbeiro_id' => $data['barbeiro_id'],
            'servico_id' => $data['servico_id'],
            'horario_id' => $data['horario_id'],
            'data' => $data['data'],
            'status' => 'A',
            'duracao_minutos' => $servico->duracao_minutos
        ]);

        if ($blocksNeeded == 1) {
            $initialSchedule->update(['disponivel' => false]);
        } else {
            foreach ($schedulesToBook as $s) {
                $s->update(['disponivel' => false]);
            }
        }

        return response()->json($appointment, 201);
    }

    public function my_appointments(Request $request)
    {
        $user = Auth::user();
        $perPage = 10;
        $page = $request->input('page', 1);
        $date = $request->input('data', null);

        $query = Agendamento::where('agendamentos.user_id', $user->id)
            ->join('users as barbeiro', 'agendamentos.barbeiro_id', '=', 'barbeiro.id')
            ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->join('horario_barbearias', 'agendamentos.horario_id', '=', 'horario_barbearias.id')
            ->select([
                'agendamentos.id',
                'agendamentos.data',
                'agendamentos.status',
                'barbeiro.name as barbeiro_nome',
                'servicos.nome as servico_nome',
                'servicos.preco as servico_preco',
                'servicos.descricao as servico_descricao',
                'servicos.duracao_minutos as servico_duracao',
                'horario_barbearias.horario_inicio',
                'horario_barbearias.horario_fim'
            ]);

        if ($date) {
            $query->whereDate('agendamentos.data', $date);
        }

        $appointments = $query->orderBy('agendamentos.data', 'desc')->orderBy('horario_barbearias.horario_inicio', 'asc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json(['success' => true, 'data' => $appointments->items(), 'current_page' => $appointments->currentPage(), 'last_page' => $appointments->lastPage(), 'total' => $appointments->total()]);
    }

    public function barber_appointments(Request $request)
    {
        $barber = Auth::user();

        $perPage = 10;
        $page = $request->input('page', 1);
        $date = $request->input('data', null);

        $query = Agendamento::where('agendamentos.barbeiro_id', $barber->id)
            ->join('users as cliente', 'agendamentos.user_id', '=', 'cliente.id')
            ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->join('horario_barbearias', 'agendamentos.horario_id', '=', 'horario_barbearias.id')
            ->select([
                'agendamentos.id',
                'agendamentos.data',
                'agendamentos.status',
                'cliente.name as cliente_nome',
                'servicos.nome as servico_nome',
                'servicos.preco as servico_preco',
                'servicos.descricao as servico_descricao',
                'servicos.duracao_minutos as servico_duracao',
                'horario_barbearias.horario_inicio',
                'horario_barbearias.horario_fim'
            ]);

        if ($date) {
            $query->whereDate('agendamentos.data', $date);
        }

        $appointments = $query->orderBy('agendamentos.data', 'desc')->orderBy('horario_barbearias.horario_inicio', 'asc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json(['success' => true, 'data' => $appointments->items(), 'current_page' => $appointments->currentPage(), 'last_page' => $appointments->lastPage(), 'total' => $appointments->total()]);
    }

    public function update_status(Request $request, $appointmentId)
    {
        $barber = Auth::user();

        $appointment = Agendamento::where('id', $appointmentId)->where('barbeiro_id', $barber->id)->first();

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found or you do not have permission.'], 404);
        }

        if ($appointment->status != 'A') {
            return response()->json(['success' => false, 'message' => 'This appointment cannot be changed because its status is not "Agendado".'], 400);
        }

        $appointment->status = 'C';
        $appointment->save();

        return response()->json(['success' => true, 'message' => 'Appointment status updated successfully.', 'data' => $appointment]);
    }
}
