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

class AgendamentoController extends Controller
{
    public function store(CreateAppointmentRequest $request)
    {
        $data = $request->validated();

        // Busca o serviço para obter a duração
    $servico = Servico::find($data['servico_id']);
        if (!$servico) {
            return response()->json(['message' => 'Serviço não encontrado'], 404);
        }

        // Busca o horário inicial selecionado
    $horarioInicial = HorarioBarbearia::find($data['horario_id']);
        if (!$horarioInicial) {
            return response()->json(['message' => 'Horário inicial não encontrado'], 404);
        }

        // Calcula quantos blocos de 15 minutos são necessários
        $blocosNecessarios = ceil($servico->duracao_minutos / 15);

        // Se for apenas 1 bloco (15 minutos), verifica apenas esse horário
        if ($blocosNecessarios == 1) {
            if (!$horarioInicial->disponivel) {
                return response()->json(['message' => 'Horário já está ocupado'], 400);
            }
        }
        // Para serviços mais longos, verifica os consecutivos
        else {
            // Pega todos os horários do barbeiro naquela data, ordenados por horário
            $horariosDoDia = HorarioBarbearia::where('user_id', $data['barbeiro_id'])
                ->where('data', $data['data'])
                ->orderBy('horario_inicio')
                ->get();

            $indiceHorarioInicial = null;
            $horariosParaAgendamento = [];

            // Encontra o índice do horário inicial na lista completa
            foreach ($horariosDoDia as $index => $horario) {
                if ($horario->id == $horarioInicial->id) {
                    $indiceHorarioInicial = $index;
                    break;
                }
            }

            if ($indiceHorarioInicial === null) {
                return response()->json(['message' => 'Horário inicial não encontrado na lista do dia'], 404);
            }

            // Verifica se há blocos suficientes consecutivos disponíveis
            for ($i = 0; $i < $blocosNecessarios; $i++) {
                $currentIndex = $indiceHorarioInicial + $i;

                // Se não existir mais horários ou não estiver disponível
                if (!isset($horariosDoDia[$currentIndex])) {
                    return response()->json(['message' => 'Tempo insuficiente para este serviço'], 400);
                }

                $currentHorario = $horariosDoDia[$currentIndex];

                // Verifica se o horário está disponível e é consecutivo
                if (!$currentHorario->disponivel) {
                    return response()->json(['message' => 'Horário já está ocupado'], 400);
                }

                // Verifica se é consecutivo (exceto para o primeiro)
                if ($i > 0) {
                    $horarioAnterior = $horariosDoDia[$currentIndex - 1];
                    if ($horarioAnterior->horario_fim != $currentHorario->horario_inicio) {
                        return response()->json(['message' => 'Horários não são consecutivos'], 400);
                    }
                }

                $horariosParaAgendamento[] = $currentHorario;
            }
        }

        // Cria o agendamento (usa apenas o primeiro horário como referência)
        $agendamento = Agendamento::create([
            'user_id' => Auth::id(),
            'barbeiro_id' => $data['barbeiro_id'],
            'servico_id' => $data['servico_id'],
            'horario_id' => $data['horario_id'],
            'data' => $data['data'],
            'status' => 'A',
            'duracao_minutos' => $servico->duracao_minutos
        ]);

        // Marca todos os horários utilizados como indisponíveis
        if ($blocosNecessarios == 1) {
            $horarioInicial->update(['disponivel' => false]);
        } else {
            foreach ($horariosParaAgendamento as $horario) {
                $horario->update(['disponivel' => false]);
            }
        }

        return response()->json($agendamento, 201);
    }

    //AGENDAMENTOS usuarioS tipo U
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

        $agendamentos = $query->orderBy('agendamentos.data', 'desc')
            ->orderBy('horario_barbearias.horario_inicio', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $agendamentos->items(),
            'current_page' => $agendamentos->currentPage(),
            'last_page' => $agendamentos->lastPage(),
            'total' => $agendamentos->total()
        ]);
    }

    public function barber_appointments(Request $request)
    {
        $barbeiro = Auth::user();

        $perPage = 10;
        $page = $request->input('page', 1);
        $date = $request->input('data', null);

        $query = Agendamento::where('agendamentos.barbeiro_id', $barbeiro->id)
            ->join('users as cliente', 'agendamentos.user_id', '=', 'cliente.id') // Dados do CLIENTE
            ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
            ->join('horario_barbearias', 'agendamentos.horario_id', '=', 'horario_barbearias.id')
            ->select([
                'agendamentos.id',
                'agendamentos.data',
                'agendamentos.status',
                'cliente.name as cliente_nome',      // Nome do cliente
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

        $agendamentos = $query->orderBy('agendamentos.data', 'desc')
            ->orderBy('horario_barbearias.horario_inicio', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $agendamentos->items(),
            'current_page' => $agendamentos->currentPage(),
            'last_page' => $agendamentos->lastPage(),
            'total' => $agendamentos->total()
        ]);
    }

    public function update_status(Request $request, $agendamentoId)
    {
        $barbeiro = Auth::user();

        $agendamento = Agendamento::where('id', $agendamentoId)
            ->where('barbeiro_id', $barbeiro->id)
            ->first();

        if (!$agendamento) {
            return response()->json([
                'success' => false,
                'message' => 'Agendamento não encontrado ou você não tem permissão para alterá-lo.'
            ], 404);
        }

        if ($agendamento->status != 'A') {
            return response()->json([
                'success' => false,
                'message' => 'Este agendamento não pode ser alterado porque seu status não é "Agendado".'
            ], 400);
        }

        $agendamento->status = 'C';
        $agendamento->save();

        return response()->json([
            'success' => true,
            'message' => 'Status do agendamento atualizado com sucesso.',
            'data' => $agendamento
        ]);
    }
}
