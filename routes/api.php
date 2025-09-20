<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\ProjetoController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post('/users/register', [AuthController::class,'register']);
Route::post('/users/login', [AuthController::class,'login']);
Route::put('/profile/update', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
// Password recovery
Route::post('/users/password/forgot', [AuthController::class, 'sendResetCode']);
Route::post('/users/password/reset', [AuthController::class, 'resetWithCode']);

//USUARIO
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/users/photo', [UserController::class, 'update_photo']);
    Route::get('/users/photo', [UserController::class, 'get_photo']);
});
//SERVICOS
Route::get('/services', [ServiceController::class, 'list'])->middleware('auth:sanctum');
Route::get('/services/by-barber', [ServiceController::class, 'list_by_barber'])->middleware('auth:sanctum');
Route::post('/services', [ServiceController::class, 'store'])->middleware('auth:sanctum');
Route::put('/services/{id}', [ServiceController::class, 'update'])->middleware('auth:sanctum');
Route::patch('/services/{id}/toggle', [ServiceController::class, 'toggle_active'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/schedules/batch-create', [ScheduleController::class, 'create_schedules']);
    Route::get('/schedules', [ScheduleController::class, 'list_by_date']);
    Route::patch('/schedules/{id}', [ScheduleController::class, 'toggle_availability']);
});

//BARBEIRO
Route::get('/barbers/approved', [UserController::class, 'list_approved_barbers'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('barbers/photos')->group(function() {
        Route::post('/', [UserController::class, 'add_photo']);
        Route::get('/', [UserController::class, 'list_photos']);
        Route::delete('/{id}', [UserController::class, 'remove_photo']);
    });
});

//AGEDAMENTO
Route::get('/barbers/{id}/schedules', [ScheduleController::class, 'list_schedules'])->middleware('auth:sanctum');
Route::post('/appointments', [AppointmentController::class, 'store'])->middleware('auth:sanctum');

//MEUS AGENDAMENTOS
Route::get('/appointments/my', [AppointmentController::class, 'my_appointments'])->middleware('auth:sanctum');
Route::get('/appointments/barber', [AppointmentController::class, 'barber_appointments'])->middleware('auth:sanctum');
Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'update_status'])->middleware('auth:sanctum');
