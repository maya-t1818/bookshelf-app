<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    /**
     * このレビューが属するユーザーを取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このレビューが属する書籍を取得
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * このレビューをいいねを取得
     */
    public function likedReviews()
    {
        return $this->belongsToMany(User::class, 'liked_reviews', 'review_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * このレビューをいいねしたユーザーを取得
     */
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'liked_reviews', 'review_id', 'user_id')
            ->withTimestamps();
    }
}
