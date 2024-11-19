<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\GenerativeAIController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('game');
Route::post('/game/check', [GameController::class, 'checkAnswer'])->name('game.check');


Route::post('/describe', [GameController::class, 'upload']);

