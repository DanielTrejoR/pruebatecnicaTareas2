<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);

    Route::get('/users/{user}/tasks', [TaskController::class, 'index']);
    Route::post('/users/{user}/tasks', [TaskController::class, 'store']);

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
});


