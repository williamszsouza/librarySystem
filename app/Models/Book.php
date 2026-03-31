<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'user_id',
        'title',
        'author',
        'description',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //livro pode ter um historico de emprestimos
    public function loans(): HasMany
    {
    return $this->hasMany(Loan::class);
    }


}
