<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo "hello world";
});

Route::get('/show_data', [MainController::class, 'show_data']);
