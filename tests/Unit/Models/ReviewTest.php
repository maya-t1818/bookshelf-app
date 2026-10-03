<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_レビューは投稿したユーザーに属すること()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $review->user);
        $this->assertEquals($user->id, $review->user->id);
    }

    public function test_レビューは対象の本に属すること()
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id]);

        $this->assertInstanceOf(Book::class, $review->book);
        $this->assertEquals($book->id, $review->book->id);
    }

    public function test_レビューにいいねしたユーザー一覧を多対多で取得できること()
    {
        $review = Review::factory()->create();
        $user = User::factory()->create();

        $review->likedByUsers()->attach($user->id);

        $this->assertTrue($review->likedByUsers->contains($user));
        $this->assertInstanceOf(User::class, $review->likedByUsers->first());
        $this->assertEquals($user->id, $review->likedByUsers->first()->id);
    }
}