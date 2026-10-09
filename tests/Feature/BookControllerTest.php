<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍一覧画面が表示できること()
    {
        $book = Book::factory()->create();

        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
        $response->assertSee($book->title);
    }

    public function test_書籍詳細画面が表示できること()
    {
        $book = Book::factory()->create();

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertSee($book->title);
    }

    public function test_ログインユーザーは書籍登録画面を表示できること()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('books.create'));

        $response->assertStatus(200);
        $response->assertViewIs('books.create');
    }

    public function test_未認証ユーザーは書籍登録画面にアクセスできないこと()
    {
        $response = $this->get(route('books.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_ログインユーザーは書籍を新規登録できること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'title'          => 'テスト書籍タイトル',
            'author'         => 'テスト著者名',
            'description'    => 'テストの概要です。',
            'isbn'           => '1234567890123',
            'published_date' => '2026-01-01',
            'image_url'      => 'https://example.com/image.jpg',
            'genres'         => [$genre->id],
        ];

        $response = $this->actingAs($user)->post(route('books.store'), $data);

        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('status', '書籍を登録しました。');

        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'title'   => 'テスト書籍タイトル',
            'isbn'    => '1234567890123',
        ]);

        $book = Book::where('title', 'テスト書籍タイトル')->first();
        $this->assertTrue($book->genres->contains($genre));
    }

    public function test_登録した本人のみが編集画面を表示できること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->get(route('books.edit', $book));
        $response->assertStatus(200);

        $response = $this->actingAs($otherUser)->get(route('books.edit', $book));
        $response->assertStatus(403);
    }

    public function test_登録した本人のみが書籍情報を更新できること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $updateData = [
            'title'          => '更新後タイトル',
            'author'         => '更新後著者',
            'description'    => '更新後の詳細文。',
            'isbn'           => '1234567890123',
            'published_date' => '2026-01-01',
            'image_url'      => 'https://example.com/new.jpg',
            'genres'         => [$genre->id],
        ];

        $response = $this->actingAs($otherUser)->put(route('books.update', $book), $updateData);
        $response->assertStatus(403);

        $response = $this->actingAs($owner)->put(route('books.update', $book), $updateData);
        $response->assertRedirect(route('books.show', $book));
        $response->assertSessionHas('status', '書籍情報を更新しました。');

        $this->assertDatabaseHas('books', [
            'id'    => $book->id,
            'title' => '更新後タイトル',
        ]);
    }

    public function test_登録した本人のみが書籍を削除できること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->delete(route('books.destroy', $book));
        $response->assertStatus(403);

        $response = $this->actingAs($owner)->delete(route('books.destroy', $book));
        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('status', '書籍情報を削除しました。');

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}