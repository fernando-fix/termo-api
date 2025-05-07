<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/palavra-aleatoria', [App\Http\Controllers\WordController::class, 'random']);
Route::get('/verificar-palavra', [App\Http\Controllers\WordController::class, 'check']);
