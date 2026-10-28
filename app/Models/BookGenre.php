<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookGenre extends Model
{
    use HasFactory;

    protected $table = 'book_genre';

    protected $fillable = [
        'book_id',
        'genre_id',
    ];

    /**
     * このBookGenreが属する書籍を取得
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * このBookGenreが属するジャンルを取得
     */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }
}
