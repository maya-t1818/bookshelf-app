<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーは読書計画一覧を表示でき登録された書籍が見えること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addDays(5),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($user)->get(route('reading-plans.index'));

        $response->assertStatus(200)
            ->assertSee($book->title);
    }

    public function test_読書計画を新しく登録できること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('reading-plans.store'), [
            'book_id' => $book->id,
            'target_date' => now()->addDays(7)->toDateString(),
        ]);

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_読書計画の目標日を更新できること()
    {
        $user = User::factory()->create();
        $plan = ReadingPlan::factory()->create(['user_id' => $user->id]);

        $newDate = '2026-11-09';

        $response = $this->actingAs($user)->put(route('reading-plans.update', $plan), [
            'target_date' => $newDate,
        ]);

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertEquals($newDate, $plan->fresh()->target_date->toDateString());
    }

    public function test_読書計画を読了にできること()
    {
        $user = User::factory()->create();
        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::InProgress,
            'completed_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('reading-plans.complete', $plan));

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        $this->assertNotNull($plan->fresh()->completed_at);
    }

    public function test_読書計画を削除できること()
    {
        $user = User::factory()->create();
        $plan = ReadingPlan::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('reading-plans.destroy', $plan));

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseMissing('reading_plans', [
            'id' => $plan->id,
        ]);
    }

    public function test_他人が作成した読書計画を更新しようとすると403エラーになること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $plan = ReadingPlan::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->put(route('reading-plans.update', $plan), [
            'target_date' => now()->addDays(5)->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_他人が作成した読書計画を削除しようとすると403エラーになること()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $plan = ReadingPlan::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->delete(route('reading-plans.destroy', $plan));

        $response->assertStatus(403);
    }
}
