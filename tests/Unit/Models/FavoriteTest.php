<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_お気に入りは対象のユーザーに属すること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertInstanceOf(User::class, $favorite->user);
        $this->assertEquals($user->id, $favorite->user->id);
    }

    public function test_お気に入りは対象の本に属すること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertInstanceOf(Book::class, $favorite->book);
        $this->assertEquals($book->id, $favorite->book->id);
    }
}