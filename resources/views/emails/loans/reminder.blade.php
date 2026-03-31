<x-mail::message>
# Olá, {{ $loan->user->name }}!

Este é um lembrete automático da nossa biblioteca. Faltam menos de 12 horas para o prazo de devolução do seu livro.

**Livro:** {{ $loan->book->title }} <br>
**Data limite para devolução:** {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y \à\s H:i') }}

Por favor, lembre-se de devolver o livro no prazo para que outros usuários também possam aproveitá-lo!


Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>