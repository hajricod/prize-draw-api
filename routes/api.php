<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrawController;
use App\Http\Controllers\TicketController;

Route::post('/draws', [DrawController::class, 'store']);
Route::get('/draws/{draw}', [DrawController::class, 'show']);
Route::post('/draws/{draw}/tickets', [TicketController::class, 'store']);
Route::post('/draws/{draw}/pick-winner', [DrawController::class, 'pickWinner']);
