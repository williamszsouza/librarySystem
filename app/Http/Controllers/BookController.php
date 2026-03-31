<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends Controller
{
    public function __construct(private BookService $bookService) 
    {
        // Protege os métodos, exceto index e show 
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        $books = $this->bookService->listBooks($request->query('search'));
        return response()->json($books);
    }

    public function store(BookRequest $request): JsonResponse
    {
        $book = $this->bookService->createBook($request->user(), $request->validated());
        return response()->json($book, 201);
    }

    public function show(Book $book): JsonResponse
    {
        return response()->json($book);
    }

    public function update(BookRequest $request, Book $book): JsonResponse
    {
        // O Laravel invoca a Policy aqui. Se não for o dono, retorna 403 Forbidden automático.
        Gate::authorize('update', $book); 
        $updatedBook = $this->bookService->updateBook($book, $request->validated());
        return response()->json($updatedBook);
    }

    public function destroy(Book $book): JsonResponse
    {
        // Invoca a Policy
        Gate::authorize('delete', $book);

        $this->bookService->deleteBook($book);
        return response()->json(null, 204);
    }
}
