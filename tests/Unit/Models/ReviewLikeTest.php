<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーがレビューにいいねを追加できること()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)
                    ->post(route('reviews.like', $review));

        $response->assertRedirect();

        $this->assertDatabaseHas('review_likes', [
            'user_id'   => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_いいね済みの状態から再度トグルするといいねが解除されること()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $user->likedReviews()->attach($review->id);

        $response = $this->actingAs($user)
                        ->post(route('reviews.like', $review));

        $response->assertRedirect();

        $this->assertDatabaseMissing('review_likes', [
            'user_id'   => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_未認証ユーザーはいいね処理を実行できないこと()
    {
        $review = Review::factory()->create();

        $response = $this->post(route('reviews.like', $review));

        $response->assertRedirect(route('login'));
    }
}