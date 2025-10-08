<?php

use App\Http\Controllers\AuthController;
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
Route::get('/draws/{draw}/summary', [DrawController::class, 'drawSummary'])->middleware('auth:sanctum');
Route::get('/draws/{draw}/full-summary', [DrawController::class, 'drawSummaryWithUsers']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/draws', [DrawController::class, 'store']);
    Route::get('/draws/{draw}', [DrawController::class, 'show']);
    Route::post('/draws/{draw}/tickets', [TicketController::class, 'store']);
    Route::post('/draws/{draw}/pick-winner', [DrawController::class, 'pickWinner']);
    Route::get('/reports/users-draws-count', [ReportController::class, 'usersDrawsTicketCount']);
    Route::get('/reports/draws/{draw}/users', [ReportController::class, 'usersTicketsByDraw']);
    Route::get('/draws/{draw}/summary', [DrawController::class, 'drawSummary'])->middleware('auth:sanctum');

});

Route::get('/draws/{draw}/full-summary', [DrawController::class, 'drawSummaryWithUsers']);
