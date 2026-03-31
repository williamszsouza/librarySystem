<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanService
{
   
    public function __construct()
    {
        //
    }


    /**
     * Porque usar return DB::transaction(function () use ($book,$user) ao invez de usar rollback e commit?
     * ao passar uma função anonima como parâmetro, o laravel gerencia o estado da transação automaticamente por baixo dos panos
     * Porque não usar try catch nessa implementação?
     * Essa foi uma decisão tomada com objetivo de manter a arquitetura mais limpa possivel, ou seja, a responsabilidade do service é apenas aplicar a regra de negocio
     * se algo falhar o trabalho dele é apenas gritar com uma 'exception', usaremos o try catch no controller pra fazer com que ele pegue essa exceção e transforme em uma mensagem de erro amigavel
     */
     /**
     * Realiza o empréstimo de um livro.
     * * @throws Exception
     */
    public function createLoan(User $user, Book $book)
    {
        if (!$book->is_available){
            throw new Exception('Este livro não está disponível para empréstimo no momento.');
        }


        // Regra de Limite: Um usuário pode ter no máximo 3 livros emprestados simultaneamente
        // Usamos o escopo 'active()' que criamos no Model de Loan
        if ($user->borrowedBooks()->active()->count() >= 3) {
            throw new Exception('Você já atingiu o limite máximo de 3 livros emprestados.');
        }

        return DB::transaction(function () use ($book,$user){
            $loan = $user->borrowedBooks()->create([
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_date' => now()->addDays(2),
                'notification_sent' => false,
            ]);

            $book->update(['is_available' => false]);

            return $loan;
        });
    }


    /**
     * Registra a devolução de um livro.
     * * @throws Exception
     */


   public function returnLoan($user, int $loanId)
    {
        $loan = \App\Models\Loan::find($loanId);

        if(!$loan){
            throw new Exception("Empréstimo não encontrado.");
        }

        if($loan->returned_at !== null) {
            throw new Exception("Este empréstimo já foi devolvido.");
        }

        return DB::transaction(function () use ($loan){
            $loan->update([
                'returned_at' => now() 
            ]);

            $loan->book()->update(['is_available' => true]);
            
            
            return $loan;
        });
    }

    /**
 * Retorna todos os empréstimos que ainda não foram devolvidos.
 * Inclui os relacionamentos de usuário e livro para evitar múltiplas queries.
 */
public function getActiveLoansDashboard()
{
    return Loan::with(['user', 'book']) // Eager Loading: Carrega os dados relacionados de uma vez
        ->whereNull('returned_at')     // Apenas os que não foram devolvidos
        ->orderBy('due_date', 'asc')   // Os que vencem primeiro aparecem no topo
        ->get();
}


    public function getLoanStats()
{
    return [
        'total_books' => Book::count(),
        'available_books' => Book::where('is_available', true)->count(),
        'active_loans' => Loan::whereNull('returned_at')->count(),
        'overdue_loans' => Loan::whereNull('returned_at')
                            ->where('due_date', '<', now())
                            ->count(),
    ];
}
 
public function getUserLoans(User $user): SupportCollection
{
    return Loan::with(['book'])
        ->where('user_id', $user->id)
        ->whereNull('returned_at')
        ->orderBy('due_date', 'asc')
        ->get()
        ->map(fn($loan) => [
            'id'              => $loan->id,
            'livro'           => $loan->book->title,
            'data_emprestimo' => $loan->borrowed_at->format('d/m/Y H:i'),
            'data_vencimento' => $loan->due_date->format('d/m/Y H:i'),
            'atrasado'        => $loan->due_date->isPast(),
        ]);
}

}
