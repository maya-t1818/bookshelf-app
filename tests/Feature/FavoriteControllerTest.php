<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーがお気に入り登録・解除・再登録できること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
                        ->from(route('books.show', $book))
                        ->post(route('favorites.toggle', $book));

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
                        ->from(route('books.show', $book))
                        ->post(route('favorites.toggle', $book));

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
                        ->from(route('books.show', $book))
                        ->post(route('favorites.toggle', $book));

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}