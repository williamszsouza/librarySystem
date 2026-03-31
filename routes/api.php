<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Não exigem login)
|--------------------------------------------------------------------------
*/

// Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Livros (Listagem e detalhes devem ser públicos)
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem token de autenticação via Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);

    // Livros (Apenas logados podem cadastrar. Edição/Deleção protegidas por Policy no Controller)
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);

    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy']);

    // Empréstimos
    Route::post('/loans', [LoanController::class, 'store']);
    Route::post('/loans/my', [LoanController::class, 'myLoans']);
    // Usamos PATCH aqui pois estamos apenas atualizando o status do empréstimo para "devolvido"
    Route::patch('/loans/{loan}/return', [LoanController::class, 'returnLoan']); 
    Route::get('/dashboard/loans', [LoanController::class, 'dashboard']);
    Route::get('/dashboard/stats', [LoanController::class, 'stats']);

});