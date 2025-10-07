<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrawController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;

Route::post('/draws', [DrawController::class, 'store']);
Route::get('/draws/{draw}', [DrawController::class, 'show']);
Route::post('/draws/{draw}/tickets', [TicketController::class, 'store']);
Route::post('/draws/{draw}/pick-winner', [DrawController::class, 'pickWinner']);
Route::get('/reports/users-draws-count', [ReportController::class, 'usersDrawsTicketCount']);
Route::get('/reports/draws/{draw}/users', [ReportController::class, 'usersTicketsByDraw']);
Route::get('/draws/{draw}/summary', [DrawController::class, 'drawSummary']);
Route::get('/draws/{draw}/full-summary', [DrawController::class, 'drawSummaryWithUsers']);
