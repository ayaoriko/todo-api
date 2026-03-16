<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TodoController;

// 認証機能なので削除

/***
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

 ***/

Route::get('/categories', [CategoryController::class, 'index']);

Route::post('/categories', [CategoryController::class, 'store']);

Route::put('/categories/{id}', [CategoryController::class, 'update']);

Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

Route::get('/todos', [TodoController::class, 'index']);

Route::post('/todos', [TodoController::class, 'store']);

Route::put('/todos/{id}/check', [TodoController::class, 'updateCheck']);

Route::put('/todos/{id}/name', [TodoController::class, 'updateName']);

Route::delete('/todos/{id}', [TodoController::class, 'destroy']);
