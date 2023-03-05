<?php

use App\Http\Controllers\ConcertOrdersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConcertsController;

Route::get('/', [ConcertsController::class, 'index']);
Route::get('concerts/{id}', [ConcertsController::class, 'show']);
Route::post('concerts/{id}/orders', [ConcertOrdersController::class, 'store']);


