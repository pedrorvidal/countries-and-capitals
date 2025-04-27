<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

Route::get('/show_data', [MainController::class, 'show_data']);

Route::get('/', [MainController::class, 'startGame'])->name('start_game');
Route::post('/', [MainController::class, 'prepareGame'])->name('prepare_game');

//in game
Route::get('/game', [MainController::class, 'game'])->name('game');
Route::get('/answer/{answer}', [MainController::class, 'answer'])->name('answer');
