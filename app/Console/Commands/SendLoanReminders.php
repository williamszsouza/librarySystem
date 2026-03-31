<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Mail\LoanDueReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLoanReminders extends Command
{
    protected $signature = 'loans:send-reminders';

    protected $description = 'Envia email para usuários com empréstimos vencendo em 12 horas ou menos';

    public function handle()
    {
        $this->info('Iniciando varredura de empréstimos...');

        // Busca todos os empréstimos ativos faltando <= 12h e que AINDA NÃO receberam o aviso
        $loans = Loan::with(['user', 'book'])
            ->whereNull('returned_at') // Ainda não devolvido
            ->where('notification_sent', false) 
            ->where('due_date', '<=', now()->addHours(12)) // Faltam 12h ou já passou
            ->get();

        if ($loans->isEmpty()) {
            $this->info('Nenhum empréstimo próximo do vencimento encontrado.');
            return;
        }

        foreach ($loans as $loan) {
            // Dispara o e-mail
            Mail::to($loan->user->email)->send(new LoanDueReminder($loan));

            // Marca para não mandar e-mail repetido na próxima hora
            $loan->update(['notification_sent' => true]);

            $this->info("Aviso enviado para: {$loan->user->email} (Livro: {$loan->book->title})");
        }

        $this->info('Varredura concluída com sucesso!');
    }
}