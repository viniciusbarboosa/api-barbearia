<?php

use App\Http\Controllers\Api\AgendamentoController;
use App\Http\Controllers\Api\AutenticadorController;
use App\Http\Controllers\Api\HorarioBarbeariaController;
use App\Http\Controllers\Api\ProjetoController;
use App\Http\Controllers\Api\ServicoController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::post('/criarUsuario', [AutenticadorController::class,'criar']);
Route::post('/logar', [AutenticadorController::class,'login']);

//USUARIO
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/usuario/foto', [UsuarioController::class, 'atualizarFoto']);
    Route::get('/usuario/foto', [UsuarioController::class, 'obterFoto']);
});
//SERVICOS
Route::get('/servicos/listar', [ServicoController::class, 'listar'])->middleware('auth:sanctum');
Route::get('/servicos/listarBarbeiro', [ServicoController::class, 'listarBarbeiro'])->middleware('auth:sanctum');
Route::post('/servicos/criar', [ServicoController::class, 'create'])->middleware('auth:sanctum');
Route::put('/servicos/{id}', [ServicoController::class, 'update'])->middleware('auth:sanctum');
Route::patch('/servicos/{id}/toggle', [ServicoController::class, 'toggleAtivo'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/horarios/criar-lote', [HorarioBarbeariaController::class, 'criarHorarios']);
    Route::get('/horarios/listar', [HorarioBarbeariaController::class, 'listarPorData']);
    Route::patch('/horarios/{id}', [HorarioBarbeariaController::class, 'toggleDisponibilidade']);
});

//BARBEIRO
Route::get('/barbeiros-aprovados', [UsuarioController::class, 'ListarBarbeiroAprovado'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('barbearia/fotos')->group(function() {
        Route::post('/', [UsuarioController::class, 'adicionarFoto']);
        Route::get('/', [UsuarioController::class, 'listarFotos']);
        Route::delete('/{id}', [UsuarioController::class, 'removerFoto']);
    });
});

//AGEDAMENTO
Route::get('/barbeiros/{id}/horarios', [HorarioBarbeariaController::class, 'listarHorarios'])->middleware('auth:sanctum');
Route::post('/agendamentos/criar', [AgendamentoController::class, 'criar'])->middleware('auth:sanctum');

//MEUS AGENDAMENTOS
Route::get('/meus-agendamentos-usuario', [AgendamentoController::class, 'meusAgendamentos'])->middleware('auth:sanctum');
Route::get('/meus-agendamentos-barbeiro', [AgendamentoController::class, 'agendamentosBarbeiro'])->middleware('auth:sanctum');
Route::get('/meus-agendamentos-barbeiro', [AgendamentoController::class, 'agendamentosBarbeiro'])->middleware('auth:sanctum');
Route::put('/agendamentos/{agendamento}/atualizar-status', [AgendamentoController::class, 'atualizarStatus'])->middleware('auth:sanctum');
