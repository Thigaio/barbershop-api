<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SchedulingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api/register');
});

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas (usuário autenticado)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('schedulings', SchedulingController::class);

    // Rotas para clientes - show, update, delete
    Route::get('clients/{id}', [ClientController::class, 'show']);
    Route::put('clients/{id}', [ClientController::class, 'update']);
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);
});

// Rotas protegidas para admins
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/clients', [ClientController::class, 'index']); 
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::put('/clients/{id}', [ClientController::class, 'update']);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
    Route::apiResource('admins', AdminController::class);
});

