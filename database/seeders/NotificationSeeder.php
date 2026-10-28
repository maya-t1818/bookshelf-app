<?php

namespace Database\Seeders;

use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // テスト用のユーザーを取得（例: ID 1 のユーザー、または最初のユーザー）
        $user = User::first();
        $plan = ReadingPlan::first(); // 適当な読書計画

        if ($user && $plan) {
            // 1. 期限3日前の通知を作成
            $user->notify(new ReadingPlanReminderNotification($plan, 'three_days_before'));

            // 2. 期限当日の通知を作成
            $user->notify(new ReadingPlanReminderNotification($plan, 'on_due_date'));

            // 3. 期限3日後の通知を作成
            $user->notify(new ReadingPlanReminderNotification($plan, 'three_days_after'));
        }
    }
}
