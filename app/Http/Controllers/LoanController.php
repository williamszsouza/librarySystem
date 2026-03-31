<?php

namespace App\Http\Controllers;

use App\Http\Requests\Loans\StoreLoanRequest;
use App\Http\Requests\StoreLoanRequest as RequestsStoreLoanRequest;
use App\Models\Book;
use App\Models\User;
use App\Services\LoanService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    protected $middlewares = ['auth:sanctum'];

    public function __construct(private LoanService $loanService)
    {
    }

    public function store(RequestsStoreLoanRequest $request): JsonResponse
    {
        $book = Book::findOrFail($request->validated('book_id'));

        try {
            $loan = $this->loanService->createLoan($request->user(), $book);
            return response()->json(['message' => 'Empréstimo realizado com sucesso.', 'loan' => $loan], 201);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 400);
        }
    }

    public function returnLoan(int $loanId): JsonResponse
    {
        try {
            $loan = $this->loanService->returnLoan(request()->user(), $loanId);
            return response()->json(['message' => 'Livro devolvido com sucesso.', 'loan' => $loan], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function dashboard(): JsonResponse
{
    $loans = $this->loanService->getActiveLoansDashboard();

    $formattedLoans = $loans->map(function ($loan) {
        return [
            'id' => $loan->id,
            'usuario' => $loan->user->name,
            'livro' => $loan->book->title,
            'data_emprestimo' => $loan->borrowed_at->format('d/m/Y H:i'),
            'data_vencimento' => $loan->due_date->format('d/m/Y H:i'),
            'atrasado' => $loan->due_date->isPast(), 
        ];
    });
    return response()->json($formattedLoans);
}

    public function stats(): JsonResponse
{
    $stats = $this->loanService->getLoanStats();

    return response()->json($stats);
}
  public function myLoans(): JsonResponse
{
    $loans = $this->loanService->getUserLoans(request()->user());
    return response()->json($loans);
}

}