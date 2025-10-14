<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BarberSchedule;
use App\Http\Requests\CreateScheduleRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function create_schedules(CreateScheduleRequest $request)
    {
        $data = $request->validated();

            $date = $data['date'] ?? $data['data'];
            $start = Carbon::parse($request->start_time ?? $request->horario_inicio_expediente);
            $end = Carbon::parse($request->end_time ?? $request->horario_fim_expediente);
        $user_id = Auth::id();

        BarberSchedule::where('user_id', $user_id)->where('date', $date)->delete();

        $created = [];
        $current = $start->copy();

        while ($current <= $end) {
            $horario = BarberSchedule::create([
                'user_id' => $user_id,
            'date' => $date,
            'start_time' => $current->format('H:i:s'),
            'end_time' => $current->addMinutes(15)->format('H:i:s'),
            'available' => true
            ]);

            $created[] = $horario;
        }

        return response()->json(['message' => 'Schedules created successfully', 'schedules' => $created], 201);
    }

    public function list_by_date(Request $request)
    {
    \Illuminate\Support\Facades\Validator::make($request->all(), ['date' => 'required|date'], [
            'required' => 'O campo :attribute é obrigatório.',
            'date' => 'O campo :attribute deve ser uma data válida.'
        ], [
        'date' => 'date'
        ])->validate();

        $schedules = BarberSchedule::where('user_id', Auth::id())->where('date', $request->date ?? $request->data)->orderBy('start_time')->get();

        return response()->json($schedules);
    }

    public function toggle_availability(Request $request, $id)
    {
        \Illuminate\Support\Facades\Validator::make($request->all(), ['disponivel' => 'required|boolean'], [
            'required' => 'O campo :attribute é obrigatório.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.'
        ], [
                'available' => 'availability'
        ])->validate();

    $schedule = BarberSchedule::where('user_id', Auth::id())->findOrFail($id);
            $schedule->update(['available' => $request->available]);

        return response()->json($schedule);
    }

    public function list_schedules($barberId, Request $request)
    {
        try {
            \Illuminate\Support\Facades\Validator::make($request->all(), ['date' => 'required|date_format:Y-m-d'], [
                'required' => 'O campo :attribute é obrigatório.',
                'date_format' => 'O campo :attribute deve ter o formato :format.'
            ], [
                    'date' => 'date'
            ])->validate();

                $schedules = BarberSchedule::where('user_id', $barberId)->where('date', $request->date ?? $request->data)->orderBy('start_time')->get()->map(function ($schedule) {
                    $booked = Appointment::where('schedule_id', $schedule->id)->where('date', $schedule->date)->exists();

                return [
                    'id' => $schedule->id,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'date' => $schedule->date,
                        'available' => $schedule->available && !$booked,
                        'booked' => $booked
                ];
            });

            return response()->json($schedules);
        } catch (\Exception $e) {
                return response()->json(['error' => 'Error fetching schedules', 'details' => $e->getMessage()], 500);
        }
    }
}
