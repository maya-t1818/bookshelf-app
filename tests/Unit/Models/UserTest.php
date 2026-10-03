<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_ユーザーは複数の本を所有できること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->books->contains($book));
        $this->assertInstanceOf(Book::class, $user->books->first());
    }

    public function test_ユーザーは複数のレビューを投稿できること()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->reviews->contains($review));
    }

    public function test_ユーザーはお気に入りした本を多対多で取得できること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book->id);

        $this->assertTrue($user->favoriteBooks->contains($book));
        $this->assertInstanceOf(Book::class, $user->favoriteBooks->first());
        $this->assertEquals($book->id, $user->favoriteBooks->first()->id);
    }

    public function test_ユーザーは複数のレビューにいいねできること()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        ReviewLike::factory()->create(['user_id' => $user->id, 'review_id' => $review->id]);

        $this->assertTrue($user->likedReviews->contains($review));
    }
}