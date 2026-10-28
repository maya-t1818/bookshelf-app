<?php

namespace App\Notifications;

use App\Models\ReadingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReadingPlanReminderNotification extends Notification
{
    use Queueable;

    protected $plan;

    protected $timing;

    // timing には 'three_days_before', 'on_due_date', 'three_days_after' を想定
    public function __construct(ReadingPlan $plan, string $timing = 'on_due_date')
    {
        $this->plan = $plan;
        $this->timing = $timing;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $titles = [
            'three_days_before' => '読書計画の期限が近づいています',
            'on_due_date' => '本日は読書計画の期限日です',
            'three_days_after' => '読書計画の期限を過ぎています',
        ];

        return [
            'title' => $titles[$this->timing] ?? '読書計画のリマインダー',
            'body' => "「{$this->plan->book->title}」の目標期日（{$this->plan->target_date->format('Y年m月d日')}）です。",
            'timing' => $this->timing,
            'reading_plan_id' => $this->plan->id,
        ];
    }
}
