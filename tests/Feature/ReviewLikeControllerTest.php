<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーがレビューのいいね登録・解除・再登録を実行できること()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)
                        ->from(route('books.show', $review->book_id))
                        ->post(route('reviews.like', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('review_likes', [
            'user_id'   => $user->id,
            'review_id' => $review->id,
        ]);

        $response = $this->actingAs($user)
                        ->from(route('books.show', $review->book_id))
                        ->post(route('reviews.like', $review));

        $response->assertRedirect();
        $this->assertDatabaseMissing('review_likes', [
            'user_id'   => $user->id,
            'review_id' => $review->id,
        ]);

        $response = $this->actingAs($user)
                        ->from(route('books.show', $review->book_id))
                        ->post(route('reviews.like', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('review_likes', [
            'user_id'   => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_未認証ユーザーはいいね処理を実行できずログイン画面へリダイレクトされること()
    {
        $review = Review::factory()->create();

        $response = $this->post(route('reviews.like', $review));

        $response->assertRedirect(route('login'));
    }
}