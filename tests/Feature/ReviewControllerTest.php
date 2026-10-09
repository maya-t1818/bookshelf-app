<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | 未認証アクセスの制限テスト
    |--------------------------------------------------------------------------
    */

    /**
     * @dataProvider unauthenticatedRouteProvider
     */
    public function test_未認証ユーザーはレビュー関連の処理を実行できずログイン画面へリダイレクトされること(string $method, string $routeName, bool $needReview = false)
    {
        $book = Book::factory()->create();
        $review = $needReview ? Review::factory()->create(['book_id' => $book->id]) : null;

        $url = match ($routeName) {
            'reviews.store'   => route('reviews.store', $book), 
            'reviews.edit'    => route('reviews.edit', $review),
            'reviews.update'  => route('reviews.update', $review),
            'reviews.destroy' => route('reviews.destroy', $review),
        };

        $response = $this->$method($url, [
            'rating'  => 5,
            'comment' => 'テストコメント',
        ]);

        $response->assertRedirect(route('login'));
    }

    public static function unauthenticatedRouteProvider(): array
    {
        return [
            'レビュー投稿' => ['post', 'reviews.store', false], 
            '編集画面表示' => ['get', 'reviews.edit', true],
            'レビュー更新' => ['put', 'reviews.update', true],
            'レビュー削除' => ['delete', 'reviews.destroy', true],
        ];
    }



    public function test_ログインユーザーは書籍にレビューを投稿できること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'rating'  => 5,
            'comment' => '最高の一冊でした！',
        ];

        $response = $this->actingAs($user)
                        ->from(route('books.show', $book))
                        ->post(route('reviews.store', $book), $data); 

        $response->assertRedirect(route('books.show', $book));
        $response->assertSessionHas('status', 'レビューを投稿しました。');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating'  => 5,
            'comment' => '最高の一冊でした！',
        ]);
    }

    public function test_投稿者本人のみがレビュー編集画面を表示できること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->get(route('reviews.edit', $review));
        $response->assertStatus(403);

        $response = $this->actingAs($owner)->get(route('reviews.edit', $review));
        $response->assertStatus(200);
        $response->assertViewIs('reviews.edit');
    }

    public function test_投稿者本人のみがレビューを更新できること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);

        $updateData = [
            'rating'  => 3,
            'comment' => '内容を変更しました。',
        ];

        $response = $this->actingAs($otherUser)
                        ->put(route('reviews.update', $review), $updateData);
        $response->assertStatus(403);

        $response = $this->actingAs($owner)
                        ->put(route('reviews.update', $review), $updateData);

        $response->assertRedirect(route('books.show', $review->book_id));
        $response->assertSessionHas('status', 'レビューを更新しました。');

        $this->assertDatabaseHas('reviews', [
            'id'      => $review->id,
            'rating'  => 3,
            'comment' => '内容を変更しました。',
        ]);
    }

    public function test_投稿者本人のみがレビューを削除できること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)
                        ->delete(route('reviews.destroy', $review));
        $response->assertStatus(403);

        $response = $this->actingAs($owner)
                        ->from(route('books.show', $review->book_id))
                        ->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('books.show', $review->book_id));
        $response->assertSessionHas('status', 'レビューを削除しました。');

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}