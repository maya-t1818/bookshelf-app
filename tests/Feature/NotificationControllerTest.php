<?php

namespace Tests\Feature;

use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーが通知一覧を表示できること()
    {
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create(['user_id' => $user->id]);

        $user->notify(new ReadingPlanReminderNotification($readingPlan, 'on_due_date'));

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notifications');
    }

    public function test_ログインユーザーが特定の通知を既読にできること()
    {
        $user = User::factory()->create();
        $readingPlan = ReadingPlan::factory()->create(['user_id' => $user->id]);

        $user->notify(new ReadingPlanReminderNotification($readingPlan, 'on_due_date'));
        $notification = $user->notifications()->first();

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($user)
            ->post(route('notifications.read', $notification->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', '通知を既読にしました。');

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
