<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_マイ読書レポート画面で基本サマリーや評価が正しく取得できること()
    {
        $user = User::factory()->create();
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book1->id, 'rating' => 4]);
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book2->id, 'rating' => 4]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'status' => ReadingPlanStatus::Completed,
        ]);
        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertStatus(200);

        $response->assertViewHas('stats', function ($stats) {
            return $stats['summary']['total_reviews'] === 2
                && $stats['summary']['books_read'] === 2
                && $stats['summary']['average_rating'] === 4;
        });
    }
}
