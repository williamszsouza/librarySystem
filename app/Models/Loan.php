<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
 protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'notification_sent',
    ];


    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
        'notification_sent' => 'boolean',
    ];

    // Pertence ao usuário que pegou emprestado
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Pertence ao livro emprestado
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    //  Facilita buscar apenas os empréstimos ativos
    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

}
