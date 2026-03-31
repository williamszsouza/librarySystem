<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class BookService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }


    public function listBooks(string $search = null):LengthAwarePaginator
    {
        $query = Book::query();

        if($search){
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->paginate(10);
    }


    public function createBook(User $user, array $data): Book
    {
        //o livro é criado atrelado ao usúario que fez a request
        return $user->registeredBooks()->create($data);
    }

    public function updateBook(Book $book, array $data): Book
    {
        $book->update($data);
        return $book;
    }

    public function deleteBook(Book $book): void
    {
        $book->delete();
    }
}
