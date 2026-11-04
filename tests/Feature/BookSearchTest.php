<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_キーワードで書籍を絞り込みできること()
    {
        $book1 = Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田太郎']);
        $book2 = Book::factory()->create(['title' => 'Vue.js実践', 'author' => '鈴木花子']);

        $response = $this->get(route('books.index', ['keyword' => 'Laravel']));

        $response->assertStatus(200)
            ->assertViewIs('books.index')

            ->assertViewHas('books', function ($books) use ($book1, $book2) {
                return $books->contains($book1) && ! $books->contains($book2);
            });
    }

    public function test_ジャンルで書籍をフィルタリングできること()
    {
        $genreA = Genre::factory()->create(['name' => 'プログラミング']);
        $genreB = Genre::factory()->create(['name' => 'デザイン']);

        $book1 = Book::factory()->create(['title' => 'PHPの本']);
        $book1->genres()->attach($genreA);

        $book2 = Book::factory()->create(['title' => 'CSSの本']);
        $book2->genres()->attach($genreB);

        $response = $this->get(route('books.index', ['genre' => $genreA->id]));

        $response->assertStatus(200)
            ->assertViewIs('books.index')
            ->assertViewHas('books', function ($books) use ($book1, $book2) {
                return $books->contains($book1) && ! $books->contains($book2);
            });
    }

    public function test_各種ソート機能が正しく動作すること()
    {

        $oldBook = Book::factory()->create([
            'title' => 'B書籍',
            'created_at' => now()->subDays(2),
        ]);
        $newBook = Book::factory()->create([
            'title' => 'A書籍',
            'created_at' => now(),
        ]);

        // デフォルト
        $responseDefault = $this->get(route('books.index'));
        $responseDefault->assertStatus(200)
            ->assertViewHas('books', function ($books) use ($newBook) {
                return $books->first()->id === $newBook->id;
            });

        // 古い順
        $responseOldest = $this->get(route('books.index', ['sort' => 'oldest']));
        $responseOldest->assertStatus(200)
            ->assertViewHas('books', function ($books) use ($oldBook) {
                return $books->first()->id === $oldBook->id;
            });

        // 2. タイトル順
        $responseTitle = $this->get(route('books.index', ['sort' => 'title']));
        $responseTitle->assertStatus(200)
            ->assertViewHas('books', function ($books) use ($newBook) {
                return $books->first()->id === $newBook->id;
            });

        // 3. 評価順
        $highRatedBook = Book::factory()->create(['title' => '高評価本']);
        $lowRatedBook = Book::factory()->create(['title' => '低評価本']);
        $user = User::factory()->create();

        Review::factory()->create(['book_id' => $highRatedBook->id, 'user_id' => $user->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $lowRatedBook->id, 'user_id' => $user->id, 'rating' => 1]);

        $responseRating = $this->get(route('books.index', ['sort' => 'rating']));
        $responseRating->assertStatus(200)
            ->assertViewHas('books', function ($books) use ($highRatedBook) {
                return $books->first()->id === $highRatedBook->id;
            });
    }
}
