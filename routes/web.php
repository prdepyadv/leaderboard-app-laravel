<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::prefix('api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'add']);
    Route::get('users/scores/report', [UserController::class, 'scoreReport']);
    Route::post('users/scores/reset', [UserController::class, 'resetScores']);
    Route::put('/user/{id}/increment', [UserController::class, 'addPoints']);
    Route::put('/user/{id}/decrement', [UserController::class, 'subPoints']);
    Route::get('/user/{id}', [UserController::class, 'findOne']);
    Route::delete('/user/{id}', [UserController::class, 'delete']);
});
