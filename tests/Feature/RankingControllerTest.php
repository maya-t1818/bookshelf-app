<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ランキング画面が正常に表示できること()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertViewIs('ranking.index');
        $response->assertViewHas('rankedBooks');
    }

    public function test_レビューの平均評価が高い順に書籍が並びレビュー未投稿の書籍は除外されること()
    {
        $highRatedBook = Book::factory()->create(['title' => '高評価の本']);
        Review::factory()->create(['book_id' => $highRatedBook->id, 'rating' => 5]);

        $lowRatedBook = Book::factory()->create(['title' => '低評価の本']);
        Review::factory()->create(['book_id' => $lowRatedBook->id, 'rating' => 3]);

        $noReviewBook = Book::factory()->create(['title' => 'レビュー無しの本']);

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);

        $rankedBooks = $response->viewData('rankedBooks');

        $this->assertCount(2, $rankedBooks);

        $this->assertEquals($highRatedBook->id, $rankedBooks->first()->id);
        $this->assertEquals($lowRatedBook->id, $rankedBooks->last()->id);

        $this->assertFalse($rankedBooks->contains('id', $noReviewBook->id));
    }

    public function test_ランキング取得件数が最大10件であること()
    {
        $books = Book::factory()->count(12)->create();
        foreach ($books as $book) {
            Review::factory()->create(['book_id' => $book->id, 'rating' => 4]);
        }

        $response = $this->get(route('ranking.index'));

        $rankedBooks = $response->viewData('rankedBooks');

        $this->assertCount(10, $rankedBooks);
    }
}