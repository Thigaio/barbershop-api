<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rota pública
Route::post('/register', [AuthController::class, 'register']);

// Rotas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
