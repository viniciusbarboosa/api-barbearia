<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BarberSchedule;
use App\Models\Service;
use App\Models\User;
use App\Http\Requests\CreateAppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(CreateAppointmentRequest $request)
    {
        $data = $request->validated();

    $servico = Service::find($data['service_id'] ?? $data['servico_id']);
        if (!$servico) {
        return response()->json(['message' => 'Service not found'], 404);
        }

    $initialSchedule = BarberSchedule::find($data['schedule_id'] ?? $data['horario_id']);
        if (!$initialSchedule) {
        return response()->json(['message' => 'Initial schedule not found'], 404);
        }

    $blocksNeeded = ceil($servico->duration_minutes / 15);

            if ($blocksNeeded == 1) {
            if (!$initialSchedule->available) {
                return response()->json(['message' => 'Schedule already booked'], 400);
            }
        } else {
            $daySchedules = BarberSchedule::where('user_id', $data['barber_id'] ?? $data['barbeiro_id'])->where('date', $data['date'] ?? $data['data'])->orderBy('start_time')->get();

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
                if (!$current->available) {
                    return response()->json(['message' => 'Schedule already booked'], 400);
                }

                if ($i > 0) {
                    $prev = $daySchedules[$currentIndex - 1];
                    if ($prev->end_time != $current->start_time) {
                        return response()->json(['message' => 'Schedules are not consecutive'], 400);
                    }
                }

                $schedulesToBook[] = $current;
            }
        }

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'barber_id' => $data['barber_id'] ?? $data['barbeiro_id'],
            'service_id' => $data['service_id'] ?? $data['servico_id'],
            'schedule_id' => $data['schedule_id'] ?? $data['horario_id'],
            'date' => $data['date'] ?? $data['data'],
            'status' => 'A',
            'duration_minutes' => $servico->duration_minutes
        ]);

        if ($blocksNeeded == 1) {
            $initialSchedule->update(['available' => false]);
        } else {
            foreach ($schedulesToBook as $s) {
                $s->update(['available' => false]);
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

        $query = Appointment::where('appointments.user_id', $user->id)
            ->join('users as barber', 'appointments.barber_id', '=', 'barber.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->join('barber_schedules', 'appointments.schedule_id', '=', 'barber_schedules.id')
            ->select([
                'appointments.id',
                'appointments.date',
                'appointments.status',
                'barber.name as barber_name',
                'services.name as service_name',
                'services.price as service_price',
                'services.description as service_description',
                'services.duration_minutes as service_duration',
                'barber_schedules.start_time',
                'barber_schedules.end_time'
            ]);

        if ($date) {
            $query->whereDate('appointments.date', $date);
        }

    $appointments = $query->orderBy('appointments.date', 'desc')->orderBy('barber_schedules.start_time', 'asc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json(['success' => true, 'data' => $appointments->items(), 'current_page' => $appointments->currentPage(), 'last_page' => $appointments->lastPage(), 'total' => $appointments->total()]);
    }

    public function barber_appointments(Request $request)
    {
        $barber = Auth::user();

        $perPage = 10;
        $page = $request->input('page', 1);
        $date = $request->input('data', null);

        $query = Appointment::where('appointments.barber_id', $barber->id)
            ->join('users as client', 'appointments.user_id', '=', 'client.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->join('barber_schedules', 'appointments.schedule_id', '=', 'barber_schedules.id')
            ->select([
                'appointments.id',
                'appointments.date',
                'appointments.status',
                'client.name as client_name',
                'services.name as service_name',
                'services.price as service_price',
                'services.description as service_description',
                'services.duration_minutes as service_duration',
                'barber_schedules.start_time',
                'barber_schedules.end_time'
            ]);

        if ($date) {
            $query->whereDate('appointments.date', $date);
        }

    $appointments = $query->orderBy('appointments.date', 'desc')->orderBy('barber_schedules.start_time', 'asc')->paginate($perPage, ['*'], 'page', $page);

        return response()->json(['success' => true, 'data' => $appointments->items(), 'current_page' => $appointments->currentPage(), 'last_page' => $appointments->lastPage(), 'total' => $appointments->total()]);
    }

    public function update_status(Request $request, $appointmentId)
    {
        $barber = Auth::user();

    $appointment = Appointment::where('id', $appointmentId)->where('barber_id', $barber->id)->first();

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found or you do not have permission.'], 404);
        }

        if ($appointment->status != 'A') {
            return response()->json(['success' => false, 'message' => 'This appointment cannot be changed because its status is not "Scheduled".'], 400);
        }

        $appointment->status = 'C';
        $appointment->save();

        return response()->json(['success' => true, 'message' => 'Appointment status updated successfully.', 'data' => $appointment]);
    }
}
