<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\CardController;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Boards
Route::apiResource('boards', BoardController::class);
Route::get('/boards', [BoardController::class, 'index']);
Route::post('/boards', [BoardController::class, 'store']);
Route::put('/boards/{id}', [BoardController::class, 'update']);

// TodoLists (inside a board)
Route::apiResource('boards.todo-lists', TodoListController::class)->shallow();
Route::get('/lists', [ToDoListController::class, 'index']); // Get all lists
Route::post('/lists', [ToDoListController::class, 'store']); // Create list
Route::delete('/lists/{id}', [ToDoListController::class, 'destroy']); // Delete list


// Cards (inside a todolist)
Route::apiResource('todo-lists.cards', CardController::class)->shallow();

// Add card (POST)
Route::post('/todo-lists/{id}/cards', [CardController::class, 'store']);

// Delete card (DELETE)
Route::delete('/cards/{id}', [CardController::class, 'destroy']);
