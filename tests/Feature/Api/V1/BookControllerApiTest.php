<?php

namespace Tests\Feature\Api\V1;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍一覧が取得できること()
    {
        $genre = Genre::factory()->create(['name' => 'プログラミング']);
        $book = Book::factory()->create([
            'title' => 'Laravel実践ガイド',
            'author' => 'テスト著者',
        ]);
        $book->genres()->attach($genre->id);

        $response = $this->getJson(route('api.v1.books.index', [
            'keyword' => 'Laravel',
            'genre_id' => $genre->id,
            'per_page' => 10,
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'isbn',
                        'description',
                        'genres',
                        'reviews_avg_rating',
                        'reviews_count',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_指定idの書籍詳細が取得できること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => '素晴らしい本でした。',
        ]);

        $response = $this->getJson(route('api.v1.books.show', $book->id));

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $book->id);

        $notFoundResponse = $this->getJson(route('api.v1.books.show', 99999));
        $notFoundResponse->assertStatus(404);
    }

    public function test_書籍を新規登録できること()
    {
        $genre = Genre::factory()->create();
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'APIテスト本',
            'author' => 'テスト太郎',
            'genre_ids' => [$genre->id], 
            'isbn' => '9784000000001',
            'published_date' => '2026-01-01',
            'description' => '登録テスト用の書籍概要です。',
];

        $response = $this->postJson(route('api.v1.books.store'), $data);



        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'APIテスト本');

        $this->assertDatabaseHas('books', [
            'isbn' => '9784000000001',
        ]);
    }

    public function test_書籍情報を更新できること()
    {
        $genre = Genre::factory()->create();
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'title' => '更新前タイトル',
            'isbn' => '9784000000001',
        ]);

        $updateData = [
            'user_id' => $user->id,
            'title' => '更新後タイトル',
            'author' => $book->author,
            'genre_ids' => [$genre->id],
            'isbn' => '9784000000001',
            'published_date' => '2026-01-01',
            'description' => '内容を修正しました。',
        ];

        $response = $this->putJson(route('api.v1.books.update', $book->id), $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', '更新後タイトル');
    }

    public function test_書籍を削除できること()
    {
        $book = Book::factory()->create();
        Review::factory()->create(['book_id' => $book->id]);

        $response = $this->deleteJson(route('api.v1.books.destroy', $book->id));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}